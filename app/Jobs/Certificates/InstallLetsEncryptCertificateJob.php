<?php declare(strict_types=1);

namespace App\Jobs\Certificates;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Certificate;
use App\Models\Project;
use App\Scripts\Root\InstallLetsEncryptCertificateScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\Root\UpdateProjectNginxConfigOnServerScript;
use App\Scripts\Root\UploadPlaceholderPageNginxConfigScript;
use App\States\Certificates\Installed;
use App\States\Certificates\Installing;
use Illuminate\Support\Facades\DB;

/*
 * TODO: CRITICAL! CONTINUE. Fail the process and notify the user if the certbot request fails.
 */

class InstallLetsEncryptCertificateJob extends AbstractRemoteServerJob
{
    protected Certificate $certificate;

    public function __construct(Certificate $certificate)
    {
        parent::__construct($certificate->server);

        $this->certificate = $certificate->withoutRelations();
    }

    public function handle(
        InstallLetsEncryptCertificateScript $installCertificate,
        UpdateProjectNginxConfigOnServerScript $updateNginxConfig,
        UploadPlaceholderPageNginxConfigScript $uploadPlaceholderNginxConfig,
        RestartNginxScript $restartNginx,
    ): void {
        DB::transaction(function () use (
            $installCertificate, $updateNginxConfig, $uploadPlaceholderNginxConfig, $restartNginx,
        ) {
            $server = $this->server->freshSharedLock();
            $certificate = $this->certificate->freshLockForUpdate();

            if (! $certificate->state->is(Installing::class))
                return;

            $rootSsh = $server->sftp('root');

            // We'll need Nginx to receive certificates, so let's ensure it is running.
            $restartNginx->execute($server, $rootSsh);

            $installCertificate->execute($server, $certificate, $rootSsh);

            /** @var Project $project */
            foreach ($server->projects as $project) {
                if ($certificate->hasDomainOf($project))
                    $updateNginxConfig->execute($server, $project, $rootSsh);
            }

            $uploadPlaceholderNginxConfig->execute($server, $rootSsh);

            $restartNginx->execute($server, $rootSsh);

            $certificate->state->transitionTo(Installed::class);

            // TODO: CRITICAL! Need to somehow verify that the certificate is received and, later, that it is installed and works.

        }, 5);
    }
}
