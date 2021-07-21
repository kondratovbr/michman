<?php declare(strict_types=1);

namespace App\Jobs\DatabaseUsers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Collections\EloquentCollection as Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GrantDatabaseUsersAccessToDatabasesJob extends AbstractJob
{
    use InteractsWithRemoteServers,
        HandlesDatabases;

    protected Collection $databaseUsers;
    protected Collection $databases;

    public function __construct(Collection $databaseUsers, Collection $databases)
    {
        $this->setQueue('servers');

        $this->databaseUsers = $databaseUsers;
        $this->databases = $databases;

        DB::beginTransaction();
        $this->databaseUsers->incrementTasks();
        $this->databases->incrementTasks();
        DB::commit();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Collection $databaseUsers */
            $databaseUsers = DatabaseUser::query()
                ->lockForUpdate()
                ->findMany($this->databaseUsers->modelKeys());

            /** @var Collection $databases */
            $databases = Database::query()
                ->lockForUpdate()
                ->findMany($this->databases->modelKeys());

            $this->runChecks($databaseUsers, $databases);

            /** @var Server $server */
            $server = Server::query()
                ->lockForUpdate()
                ->findOrFail($databases->first()->server->getKey());

            $ssh = $server->sftp();
            $grantScript = $this->getDatabaseScript($server, 'GrantDatabaseUserAccessToDatabaseScript');

            /** @var DatabaseUser $databaseUser */
            foreach ($databaseUsers as $databaseUser) {
                /** @var Database $database */
                foreach ($databases as $database) {
                    $grantScript->execute(
                        $server,
                        $database->name,
                        $databaseUser->name,
                        $ssh,
                    );
                }
            }

            $databaseUsers->decrementTasks();
            $databases->decrementTasks();
        }, 5);
    }

    /**
     * Check that databases and database users aren't empty and all belong to the same server.
     */
    private function runChecks(Collection $databaseUsers, Collection $databases): void
    {
        if ($databaseUsers->isEmpty())
            throw new RuntimeException('No database users found.');

        if ($databases->isEmpty())
            throw new RuntimeException('No databases found.');

        if ($databaseUsers->pluck('server_id')->unique()->count() > 1)
            throw new RuntimeException('The database users belong to different servers.');

        if ($databases->pluck('server_id')->unique()->count() > 1)
            throw new RuntimeException('The databases belong to different servers.');

        if (! $databaseUsers->first()->server->is($databases->first()->server))
            throw new RuntimeException('The databases and database users belong to different servers.');
    }
}
