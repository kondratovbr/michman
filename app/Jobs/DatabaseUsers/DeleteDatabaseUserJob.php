<?php declare(strict_types=1);

namespace App\Jobs\DatabaseUsers;

use App\Jobs\AbstractRemoteServerJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Models\DatabaseUser;
use Illuminate\Support\Facades\DB;

class DeleteDatabaseUserJob extends AbstractRemoteServerJob
{
    use HandlesDatabases;

    protected DatabaseUser $databaseUser;

    public function __construct(DatabaseUser $databaseUser)
    {
        parent::__construct($databaseUser->server);

        $this->databaseUser = $databaseUser->withoutRelations();

        $this->databaseUser->incrementTasks();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $databaseUser = $this->databaseUser->freshLockForUpdate();
            $server = $this->server->freshSharedLock();
            if ($databaseUser->project()->exists())
                $databaseUser->project()->lockForUpdate()->firstOrFail();

            $this->getDatabaseScript(
                $server,
                'DeleteDatabaseUserScript',
            )->execute(
                $server,
                $databaseUser->name,
            );

            if (! is_null($databaseUser->project)) {
                $databaseUser->project->databaseUser()->disassociate();
                $databaseUser->project->save();
            }

            $databaseUser->purge();
        }, 5);
    }
}
