<?php declare(strict_types=1);

namespace App\Actions\Certificates;

use App\Jobs\Certificates\DeleteLetsEncryptCertificateJob;
use App\Models\Certificate;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeleteCertificateAction
{
    public function execute(Certificate $certificate): void
    {
        DB::transaction(function () use ($certificate) {
            $certificate = $certificate->freshLockForUpdate();

            $certificate->status = Certificate::STATUS_DELETING;
            $certificate->save();

            // TODO: CRITICAL! Don't forget to implement the rest as well, if I have any.
            if ($certificate->type !== Certificate::TYPE_LETS_ENCRYPT)
                throw new RuntimeException("The certificate with ID $certificate->id is not of the 'lets-encrypt' type, but deletion is implemented for that type only.");

            DeleteLetsEncryptCertificateJob::dispatch($certificate);
        }, 5);
    }
}
