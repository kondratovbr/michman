<?php declare(strict_types=1);

namespace App\Jobs\Databases;

use App\Jobs\AbstractRemoteServerJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Models\Database;
use Illuminate\Support\Facades\DB;

class CreateDatabaseOnServerJob extends AbstractRemoteServerJob
{
    use HandlesDatabases;

    protected Database $database;

    public function __construct(Database $database, bool $sync = false)
    {
        parent::__construct($database->server)->sync($sync);

        $this->database = $database->withoutRelations();

        $this->database->incrementTasks();
    }

    public function handle(): void
    {
        DB::transaction(function() {
            $database = $this->database->freshLockForUpdate();
            $server = $this->server->freshSharedLock();

            $this->getDatabaseScript(
                $server,
                'CreateDatabaseScript',
            )->execute(
                $server,
                $database->name,
            );

            $database->decrementTasks();
        }, 5);
    }
}
