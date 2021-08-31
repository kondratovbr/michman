<?php declare(strict_types=1);

namespace App\Jobs\Certificates;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Certificate;
use App\Models\Server;
use App\Scripts\Root\InstallLetsEncryptCertificateScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\Root\UpdateProjectNginxConfigOnServerScript;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/*
 * TODO: CRITICAL! Make sure a user cannot start SSL installation before installing a repo, otherwise this whole logic won't work.
 */

/*
 * TODO: CRITICAL! I need to update a placeholder site config somewhere here as well - add HTTPS to it once the certificate is in place.
 */

/*
 * TODO: CRITICAL! How to handle multiple certificates for a project? Probably have to make the "server-ssl" config quite a lot more complex to handle that situation. A single project may be served on multiple domains, so multiple certificates can exist. Try how Forge does it, btw. Luckily, I have a spare domain.
 */

class InstallLetsEncryptCertificateJob extends AbstractRemoteServerJob
{
    protected Certificate $certificate;

    public function __construct(Certificate $certificate, Server $server)
    {
        parent::__construct($server);

        $this->certificate = $certificate->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(
        InstallLetsEncryptCertificateScript $installCertificate,
        UpdateProjectNginxConfigOnServerScript $updateNginxConfig,
        RestartNginxScript $restartNginx,
    ): void {
        DB::transaction(function () use (
            $installCertificate, $updateNginxConfig, $restartNginx,
        ) {
            $server = $this->server->freshLockForUpdate();
            $certificate = $this->certificate->freshLockForUpdate();
            $project = $certificate->project;

            if (! $server->certificates->contains($certificate)) {
                Log::warning('InstallLetsEncryptCertificateJob: This Server model doesn\'t have this Certificate model attached.');
                return;
            }

            $rootSsh = $server->sftp('root');

            $installCertificate->execute($server, $certificate, $rootSsh);

            $updateNginxConfig->execute($server, $project, $rootSsh);

            $restartNginx->execute($server, $rootSsh);

            // TODO: CRITICAL! Need to somehow verify that the certificate is received and, later, that it is installed and works.

        }, 5);
    }
}
