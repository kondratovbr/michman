<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallGunicornJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->setQueue('servers');

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! Arr::hasValue(config("servers.types.{$server->type}.install"), 'nginx')) {
                $this->fail(new RuntimeException('This type of server should not have Gunicorn installed.'));
                return;
            }

            // TODO: CRITICAL! Implement.

            //
        }, 5);
    }
}
