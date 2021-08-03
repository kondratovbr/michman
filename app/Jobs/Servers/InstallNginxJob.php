<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Server;
use App\Scripts\Root\InstallNginxScript;
use App\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallNginxJob extends AbstractRemoteServerJob
{
    protected Server $server;

    public function __construct(Server $server)
    {
        parent::__construct($server);

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(InstallNginxScript $installNginx): void
    {
        DB::transaction(function () use ($installNginx) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! Arr::hasValue(config("servers.types.{$server->type}.install"), 'nginx')) {
                $this->fail(new RuntimeException('This type of server should not have Nginx installed.'));
                return;
            }

            $installNginx->execute($server);
        }, 5);
    }
}
