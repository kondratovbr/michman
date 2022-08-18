<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractRemoteServerJob;
use App\Scripts\Root\InstallNginxScript;
use App\Scripts\Root\RestartNginxScript;
use App\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallNginxJob extends AbstractRemoteServerJob
{
    public function handle(
        InstallNginxScript $installNginx,
        RestartNginxScript $restartNginx,
    ): void {
        DB::transaction(function () use (
            $installNginx, $restartNginx
        ) {
            $server = $this->server->freshLockForUpdate();

            if (! Arr::hasValue(config("servers.types.{$server->type}.install"), 'nginx')) {
                $this->fail(new RuntimeException('This type of server should not have Nginx installed.'));
                return;
            }

            $ssh = $server->sftp();

            $installNginx->execute($server, $ssh);

            $restartNginx->execute($server, $ssh);
        });
    }
}
