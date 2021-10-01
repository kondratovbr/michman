<?php declare(strict_types=1);

namespace App\Jobs\DatabaseUsers;

use App\Jobs\AbstractRemoteServerJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Collections\EloquentCollection as Collection;
use Illuminate\Support\Facades\DB;

class RevokeDatabaseUsersAccessToDatabasesJob extends AbstractRemoteServerJob
{
    use HandlesDatabases;

    protected Collection $databaseUsers;
    protected Collection $databases;

    public function __construct(Collection $databaseUsers, Collection $databases)
    {
        parent::__construct($databaseUsers->first()->server);

        $this->databaseUsers = $databaseUsers;
        $this->databases = $databases;

        DB::beginTransaction();
        $this->databaseUsers->incrementTasks();
        $this->databases->incrementTasks();
        DB::commit();
    }

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

            $this->validateDatabasesAndUsers($databaseUsers, $databases);

            /** @var Server $server */
            $server = Server::query()
                ->sharedLock()
                ->findOrFail($databases->first()->server->getKey());

            $ssh = $server->sftp();
            $script = $this->getDatabaseScript($server, 'RevokeDatabaseUserAccessToDatabaseScript');

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

            $databaseUsers->decrementTasks();
            $databases->decrementTasks();
        }, 5);
    }
}
