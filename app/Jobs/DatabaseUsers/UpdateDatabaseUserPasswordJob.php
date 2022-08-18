<?php declare(strict_types=1);

namespace App\Jobs\DatabaseUsers;

use App\Jobs\AbstractRemoteServerJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Models\DatabaseUser;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UpdateDatabaseUserPasswordJob extends AbstractRemoteServerJob
{
    use HandlesDatabases;

    protected DatabaseUser $databaseUser;

    public function __construct(DatabaseUser $databaseUser)
    {
        parent::__construct($databaseUser->server);

        $this->databaseUser = $databaseUser;

        $this->databaseUser->incrementTasks();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $databaseUser = $this->databaseUser->freshLockForUpdate();
            $server = $this->server->freshSharedLock();

            if (empty($databaseUser->password ?? null)) {
                $this->fail(new RuntimeException('Database user has no password stored in the DB.'));
                return;
            }

            $server = $databaseUser->server;

            $script = $this->getDatabaseScript($server, 'UpdateDatabaseUserPasswordScript');

            $script->execute(
                $server,
                $databaseUser->name,
                $databaseUser->password,
            );

            $databaseUser->password = null;
            $databaseUser->save();

            $databaseUser->decrementTasks();
        });
    }
}
