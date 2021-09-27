<?php declare(strict_types=1);

namespace App\Actions\Certificates;

use App\Jobs\Certificates\InstallLetsEncryptCertificateJob;
use App\Models\Certificate;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class StoreLetsEncryptCertificateAction
{
    /** @param string[] $domains */
    public function execute(Server $server, array $domains): Certificate
    {
        return DB::transaction(function () use ($server, $domains): Certificate {
            /** @var Certificate $certificate */
            $certificate = $server->certificates()->create([
                'type' => Certificate::TYPE_LETS_ENCRYPT,
                'domains' => $domains,
                'status' => Certificate::STATUS_INSTALLING,
            ]);

            InstallLetsEncryptCertificateJob::dispatch($certificate);

            return $certificate;
        }, 5);
    }
}
