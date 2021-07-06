<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Events\Databases\DatabaseDeletedEvent;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Database;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// TODO: CRITICAL! Cover with tests!

class DeleteDatabaseJob extends AbstractJob
{
    use InteractsWithRemoteServers,
        HandlesDatabases;

    protected Database $database;

    public function __construct(Database $database)
    {
        $this->setQueue('servers');

        $this->database = $database->withoutRelations();
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

            event(new DatabaseDeletedEvent($server));
        }, 5);
    }
}
