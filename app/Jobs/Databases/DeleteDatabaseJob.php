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

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Database $database */
            $database = Database::query()
                ->with('server')
                ->lockForUpdate()
                ->findOrFail($this->database->getKey());

            $server = $database->server;

            $this->getDatabaseScript(
                $server,
                'DeleteDatabaseScript',
            )->execute(
                $server,
                $database->name,
            );

            $database->delete();
        }, 5);
    }
}
