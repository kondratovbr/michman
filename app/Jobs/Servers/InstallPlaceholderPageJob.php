<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractRemoteServerJob;
use App\Scripts\Root\EnablePlaceholderSiteScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\Root\UploadPlaceholderPageNginxConfigScript;
use App\Scripts\User\UploadPlaceholderPageScript;
use Illuminate\Support\Facades\DB;

class InstallPlaceholderPageJob extends AbstractRemoteServerJob
{
    public function handle(
        UploadPlaceholderPageScript $uploadPlaceholderPage,
        UploadPlaceholderPageNginxConfigScript $uploadPlaceholderNginxConfig,
        EnablePlaceholderSiteScript $enablePlaceholderSite,
        RestartNginxScript $restartNginx,
    ): void {
        DB::transaction(function () use (
            $uploadPlaceholderPage, $uploadPlaceholderNginxConfig, $enablePlaceholderSite, $restartNginx
        ) {
            $server = $this->server->freshLockForUpdate();

            $michmanSsh = $server->sftp(config('servers.worker_user'));
            $rootSsh = $server->sftp();

            $uploadPlaceholderPage->execute($server, $michmanSsh);

            $uploadPlaceholderNginxConfig->execute($server, $rootSsh);

            $enablePlaceholderSite->execute($server, $rootSsh);

            $restartNginx->execute($server, $rootSsh);
        }, 5);
    }
}
