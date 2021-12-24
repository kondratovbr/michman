<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractRemoteServerJob;
use App\Scripts\Root\EnablePlaceholderSiteScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\Root\UploadPlaceholderPageNginxConfigScript;
use App\Scripts\User\UploadPlaceholderPageScript;
use Illuminate\Support\Facades\DB;

/*
 * TODO: CRITICAL! CONTINUE. This job should install the placeholder page on a server even if the server has no project installed.
 *       This will allow to show the user that the server is operational,
 *       and to receive Let's Encrypt certificates independent of projects.
 *       Also, project installation won't conflict with the certificate installation.
 *       Make sure to disable placeholder installation in the project installation logic and test the whole thing.
 */

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
