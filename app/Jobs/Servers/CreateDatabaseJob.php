<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Database;
use App\Models\Server;
use App\Scripts\Root\Mysql8_0\CreateDatabaseScript;
use Illuminate\Support\Facades\DB;

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
    public function handle(CreateDatabaseScript $createDatabase): void
    {
        // TODO: CRITICAL! Implement.

        DB::transaction(function() use ($createDatabase) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            /** @var Database $database */
            $database = $server->databases()->create([
                'name' => $this->dbName,
            ]);

            $createDatabase->execute($server, $database->name);

        }, 5);
    }
}
