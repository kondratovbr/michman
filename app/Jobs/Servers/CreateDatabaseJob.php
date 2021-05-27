<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Database;
use App\Models\Server;
use App\Scripts\Root\Mysql8_0\CreateDatabaseScript;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CreateDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, InteractsWithRemoteServers;

    protected Server $server;
    protected string $dbName;

    public function __construct(Server $server, string $dbName)
    {
        $this->onQueue('servers');

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
