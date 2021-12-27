<?php declare(strict_types=1);

namespace App\Actions\Certificates;

use App\Jobs\Certificates\InstallLetsEncryptCertificateJob;
use App\Models\Certificate;
use App\Models\Server;
use App\States\Certificates\Installing;
use Ds\Set;
use Illuminate\Support\Facades\DB;

class StoreLetsEncryptCertificateAction
{
    /** @param string[] $domains */
    public function execute(Server $server, array $domains): Certificate
    {
        return DB::transaction(function () use ($server, $domains): Certificate {
            /** @var Certificate $certificate */
            $certificate = $server->certificates()->create([
                'type' => Certificate::TYPE_LETS_ENCRYPT,
                'domains' => new Set($domains),
                'state' => Installing::class,
            ]);

            InstallLetsEncryptCertificateJob::dispatch($certificate);

            return $certificate;
        }, 5);
    }
}
