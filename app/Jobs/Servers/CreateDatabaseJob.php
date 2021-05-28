<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Database;
use App\Models\Server;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateDatabaseJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;
    protected string $dbName;

    public function __construct(Server $server, string $dbName)
    {
        $this->queue('servers');

        $this->server = $server->withoutRelations();
        $this->dbName = $dbName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function() {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (empty($server->installedDatabase))
                throw new RuntimeException('No database installed on this server.');

            /** @var Database $database */
            $database = $server->databases()->create([
                'name' => $this->dbName,
            ]);

            $scriptClass = (string) config("servers.databases.{$server->installedDatabase}.scripts_namespace") . '\CreateDatabaseScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No database creation script exists for this database.');

            $script = App::make($scriptClass);

            $script->execute($server, $database->name);

        }, 5);
    }
}
