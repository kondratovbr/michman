<?php declare(strict_types=1);

namespace App\Jobs\Certificates;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Certificate;
use App\Models\Project;
use App\Notifications\Servers\FailedToInstallCertificateNotification;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\InstallLetsEncryptCertificateScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\Root\UpdateProjectNginxConfigOnServerScript;
use App\Scripts\Root\UploadPlaceholderPageNginxConfigScript;
use App\States\Certificates\Installed;
use App\States\Certificates\Installing;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

// TODO: IMPORTANT! As a part of a later health checking feature need to verify HTTPS works based on these certificates.

class InstallLetsEncryptCertificateJob extends AbstractRemoteServerJob
{
    protected Certificate $certificate;

    protected CarbonInterface|null $logFrom = null;
    protected CarbonInterface|null $logTo = null;

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

            $this->logFrom = now();

            try {
                $installCertificate->execute($server, $certificate, $rootSsh);
            } catch (ServerScriptException) {
                $this->logTo = now();
                $this->handleFailure();
                return;
            }

            /** @var Project $project */
            foreach ($server->projects as $project) {
                if ($certificate->hasDomainOf($project))
                    $updateNginxConfig->execute($server, $project, $rootSsh);
            }

            $uploadPlaceholderNginxConfig->execute($server, $rootSsh);

            $restartNginx->execute($server, $rootSsh);

            $this->logTo = now();

            $certificate->state->transitionTo(Installed::class);
        });
    }

    public function handleFailure(): void
    {
        $this->server->user->notify(new FailedToInstallCertificateNotification(
            $this->server, $this->logFrom, $this->logTo
        ));

        $this->certificate->purge();
    }

    public function failed(): void
    {
        $this->handleFailure();
    }
}
