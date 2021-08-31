<?php declare(strict_types=1);

namespace App\Actions\Certificates;

use App\Jobs\Certificates\InstallLetsEncryptCertificateJob;
use App\Models\Certificate;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class StoreLetsEncryptCertificateAction
{
    /**
     * @param string[] $domains
     */
    public function execute(Project $project, array $domains): Certificate
    {
        return DB::transaction(function () use ($project, $domains): Certificate {
            /** @var Certificate $certificate */
            $certificate = $project->certificates()->create([
                'type' => Certificate::TYPE_LETS_ENCRYPT,
                'domains' => $domains,
            ]);

            $certificate->servers()->sync($project->servers);

            // TODO: CRITICAL! CONTINUE. Implement a job to handle the certificate request process - replace the Nginx config with the SSL one if necessary and request the certificate using Certbot. Don't forget to implement Certificate properties/states to show the situation to the user. And don't forget to update the deployment logic - need to put a different Nginx config if HTTPS is used.

            Bus::chain($certificate->servers->map(fn(Server $server) =>
            new InstallLetsEncryptCertificateJob($certificate, $server)
            )->toArray())->dispatch();

            return $certificate;
        }, 5);
    }
}
