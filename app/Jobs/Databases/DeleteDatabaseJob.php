<?php declare(strict_types=1);

namespace App\Jobs\Databases;

use App\Jobs\AbstractRemoteServerJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Models\Database;
use Illuminate\Support\Facades\DB;

class DeleteDatabaseJob extends AbstractRemoteServerJob
{
    use HandlesDatabases;

    protected Database $database;

    public function __construct(Database $database)
    {
        parent::__construct($database->server);

        $this->database = $database->withoutRelations();

        $this->database->incrementTasks();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $database = $this->database->freshLockForUpdate();
            $server = $this->server->freshSharedLock();
            if ($database->project()->exists())
                $database->project()->lockForUpdate()->firstOrFail();

            $this->getDatabaseScript(
                $server,
                'DeleteDatabaseScript',
            )->execute(
                $server,
                $database->name,
            );

            if (! is_null($database->project)) {
                $database->project->database()->disassociate();
                $database->project->save();
            }

            $database->delete();
        }, 5);
    }
}
