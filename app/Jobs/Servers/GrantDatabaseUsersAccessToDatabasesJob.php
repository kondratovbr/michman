<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
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
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $databaseUsers = DatabaseUser::query()
                ->lockForUpdate()
                ->findMany($this->databaseUsers->modelKeys());

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

            $this->updateStatuses($databaseUsers, $databases);
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

    /**
     * Set statuses to CREATED.
     */
    private function updateStatuses(Collection $databaseUsers, Collection $databases): void
    {
        // We don't do mass updates here because we're in an asynchronous job
        // and we need to send events anyway.
        foreach ($databaseUsers as $databaseUser) {
            $databaseUser->status = DatabaseUser::STATUS_CREATED;
            $databaseUser->save();
        }
        foreach ($databases as $database) {
            $database->status = Database::STATUS_CREATED;
            $database->save();
        }
    }
}
