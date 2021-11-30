<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithServerProviders;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

class GetServerPublicIpJob extends AbstractJob
{
    use InteractsWithServerProviders;

    /** @var int The amount of seconds to wait between retries if an address wasn't issued yet. */
    protected const SECONDS_BETWEEN_RETRIES = 30;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->setQueue('providers');

        $this->server = $server->withoutRelations();
    }

    public function handle(): void
    {
        $api = $this->server->provider->api();

        DB::transaction(function () use ($api) {
            $server = $this->server->freshLockForUpdate();

            $ip = $api->getServerPublicIp4($server->externalId);

            if (is_null($ip)) {
                $this->release(static::SECONDS_BETWEEN_RETRIES);
                return;
            }

            $server->publicIp = $ip;
            $server->save();
        }, 5);
    }
}
