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

            Bus::chain($certificate->servers->map(fn(Server $server) =>
                new InstallLetsEncryptCertificateJob($certificate, $server)
            )->toArray())->dispatch();

            return $certificate;
        }, 5);
    }
}
