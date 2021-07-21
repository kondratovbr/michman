<?php declare(strict_types=1);

namespace App\Jobs\Databases;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Database;
use Illuminate\Support\Facades\DB;

class CreateDatabaseOnServerJob extends AbstractJob
{
    use InteractsWithRemoteServers,
        HandlesDatabases;

    protected Database $database;

    public function __construct(Database $database)
    {
        $this->setQueue('servers');

        $this->database = $database->withoutRelations();

        $this->database->incrementTasks();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function() {
            /** @var Database $database */
            $database = Database::query()
                ->with('server')
                ->lockForUpdate()
                ->findOrFail($this->database->getKey());

            $server = $database->server;

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
