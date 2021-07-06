<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Events\Databases\DatabaseCreatedEvent;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Database;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// TODO: CRITICAL! Cover with tests!

class CreateDatabaseOnServerJob extends AbstractJob
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

            if ($database->status === Database::STATUS_CREATING)
                $database->status = Database::STATUS_CREATED;

            $database->save();

            event(new DatabaseCreatedEvent($database));
        }, 5);
    }
}
