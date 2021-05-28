<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;

class InstallCacheJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;
    protected string $cache;

    public function __construct(Server $server, string $cache)
    {
        $this->setQueue('default');

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
