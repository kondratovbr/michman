<?php declare(strict_types=1);

namespace App\Actions\Certificates;

use App\Jobs\Certificates\InstallLetsEncryptCertificateJob;
use App\Models\Certificate;
use App\Models\Server;
use App\States\Certificates\Installing;
use Illuminate\Support\Facades\DB;

class StoreLetsEncryptCertificateAction
{
    /** @param string[] $domain */
    public function execute(Server $server, string $domain): Certificate
    {
        return DB::transaction(function () use ($server, $domain): Certificate {
            /** @var Certificate $certificate */
            $certificate = $server->certificates()->create([
                'type' => Certificate::TYPE_LETS_ENCRYPT,
                'domain' => $domain,
                'state' => Installing::class,
            ]);

            InstallLetsEncryptCertificateJob::dispatch($certificate);

            return $certificate;
        }, 5);
    }
}
