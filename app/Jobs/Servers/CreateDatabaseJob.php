<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
    public function handle(): void
    {
        // TODO: CRITICAL! Implement.

        //
    }
}
