<?php declare(strict_types=1);

namespace App\Jobs\Certificates;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Certificate;
use App\Models\Project;
use App\Scripts\Root\InstallLetsEncryptCertificateScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\Root\UpdateProjectNginxConfigOnServerScript;
use App\Scripts\Root\UploadPlaceholderPageNginxConfigScript;
use Illuminate\Support\Facades\DB;

/*
 * TODO: CRITICAL! Make sure a user cannot start SSL installation before installing a repo, otherwise this whole logic won't work. Or rather install some different placeholder before that, so we have something to serve the ACME challenge from.
 */

/*
 * TODO: CRITICAL! Make sure a certificate cannot be requested for a server of a type that shouldn't be accessible from the outside anyway. I.e. certificates are only for "app", "web" and "balancer" types of servers.
 */

class InstallLetsEncryptCertificateJob extends AbstractRemoteServerJob
{
    protected Certificate $certificate;

    public function __construct(Certificate $certificate)
    {
        parent::__construct($certificate->server);

        $this->certificate = $certificate->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(
        InstallLetsEncryptCertificateScript $installCertificate,
        UpdateProjectNginxConfigOnServerScript $updateNginxConfig,
        UploadPlaceholderPageNginxConfigScript $uploadPlaceholderNginxConfig,
        RestartNginxScript $restartNginx,
    ): void {
        DB::transaction(function () use (
            $installCertificate, $updateNginxConfig, $uploadPlaceholderNginxConfig, $restartNginx,
        ) {
            $server = $this->server->freshLockForUpdate();
            $certificate = $this->certificate->freshLockForUpdate();

            $rootSsh = $server->sftp('root');

            // We'll need Nginx to receive certificates, so let's ensure it is running.
            $restartNginx->execute($server, $rootSsh);

            $installCertificate->execute($server, $certificate, $rootSsh);

            /** @var Project $project */
            foreach ($server->projects as $project) {
                if ($certificate->hasDomainOf($project)) {
                    $updateNginxConfig->execute($server, $project, $rootSsh);
                    $uploadPlaceholderNginxConfig->execute($server, $project, $rootSsh);
                }
            }

            $restartNginx->execute($server, $rootSsh);

            $certificate->status = Certificate::STATUS_INSTALLED;
            $certificate->save();

            // TODO: CRITICAL! Need to somehow verify that the certificate is received and, later, that it is installed and works.

        }, 5);
    }
}
