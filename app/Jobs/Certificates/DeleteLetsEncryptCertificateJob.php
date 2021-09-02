<?php declare(strict_types=1);

namespace App\Jobs\Certificates;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Certificate;
use App\Models\Project;
use App\Scripts\Root\DeleteLetsEncryptCertificateScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\Root\UpdateProjectNginxConfigOnServerScript;
use App\Scripts\Root\UploadPlaceholderPageNginxConfigScript;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Test and cover with tests.

class DeleteLetsEncryptCertificateJob extends AbstractRemoteServerJob
{
    protected Certificate $certificate;

    public function __construct(Certificate $certificate)
    {
        parent::__construct($certificate->server);

        $this->certificate = $certificate->withoutRelations();
    }

    public function handle(
        DeleteLetsEncryptCertificateScript $deleteCertificate,
        UpdateProjectNginxConfigOnServerScript $updateNginxConfig,
        UploadPlaceholderPageNginxConfigScript $uploadPlaceholderNginxConfig,
        RestartNginxScript $restartNginx,
    ): void {
        DB::transaction(function () use (
            $deleteCertificate, $updateNginxConfig, $uploadPlaceholderNginxConfig, $restartNginx,
        ) {
            $server = $this->lockServer();
            $cert = $this->certificate->freshLockForUpdate();

            $rootSsh = $server->sftp('root');

            $deleteCertificate->execute($server, $cert, $rootSsh);

            $cert->delete();

            /** @var Project $project */
            foreach ($server->projects as $project) {
                if ($cert->hasDomainOf($project)) {
                    $updateNginxConfig->execute($server, $project, $rootSsh);
                    $uploadPlaceholderNginxConfig->execute($server, $project, $rootSsh);
                }
            }

            $restartNginx->execute($server, $rootSsh);
        }, 5);
    }
}
