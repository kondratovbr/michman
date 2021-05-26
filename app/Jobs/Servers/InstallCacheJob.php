<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InstallCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, InteractsWithRemoteServers;

    protected Server $server;
    protected string $cache;

    public function __construct(Server $server, string $cache)
    {
        $this->onQueue('default');

        $this->server = $server->withoutRelations();
        $this->cache = $cache;
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
