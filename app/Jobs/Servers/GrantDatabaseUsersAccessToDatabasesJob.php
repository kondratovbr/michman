<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Collections\EloquentCollection;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
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
            /** @var EloquentCollection $databaseUsers */
            $databaseUsers = DatabaseUser::query()
                ->lockForUpdate()
                ->findMany($this->databaseUsers->modelKeys());

            /** @var EloquentCollection $databases */
            $databases = Database::query()
                ->lockForUpdate()
                ->findMany($this->databases->modelKeys());

            $this->runChecks($databaseUsers, $databases);

            /** @var Server $server */
            $server = Server::query()
                ->lockForUpdate()
                ->findOrFail($databases->first()->server->getKey());

            $ssh = $server->sftp();
            $script = $this->getDatabaseScript($server, 'GrantDatabaseUserAccessToDatabaseScript');

            /** @var DatabaseUser $databaseUser */
            foreach ($databaseUsers as $databaseUser) {
                /** @var Database $database */
                foreach ($databases as $database) {
                    $script->execute(
                        $server,
                        $database->name,
                        $databaseUser->name,
                        $ssh,
                    );
                }
            }

            $databaseUsers->updateStatus(DatabaseUser::STATUS_CREATED);
            $databases->updateStatus(Database::STATUS_CREATED);

            foreach ($databaseUsers as $databaseUser)
                event(new DatabaseUserUpdatedEvent($databaseUser));

            foreach ($databases as $database)
                event(new DatabaseUpdatedEvent($database));
        }, 5);
    }

    /**
     * Check that databases and database users aren't empty and all belong to the same server.
     */
    protected function runChecks(Collection $databaseUsers, Collection $databases): void
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
