<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Events\Databases\DatabaseCreatedEvent;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Database;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// TODO: CRITICAL! Cover with tests!

class CreateDatabaseOnServerJob extends AbstractJob
{
    use InteractsWithRemoteServers;

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

            if (empty($server->installedDatabase))
                throw new RuntimeException('No database installed on this server.');

            $scriptClass = (string) config("servers.databases.{$server->installedDatabase}.scripts_namespace")
                . '\CreateDatabaseScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No database creation script exists for this database.');

            $script = App::make($scriptClass);

            $script->execute($server, $database->name);

            $database->status = Database::STATUS_CREATED;
            $database->save();

            event(new DatabaseCreatedEvent($database));
        }, 5);
    }
}
