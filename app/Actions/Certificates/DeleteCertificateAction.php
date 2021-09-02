<?php declare(strict_types=1);

namespace App\Actions\Certificates;

use App\Jobs\Certificates\DeleteCertificateJob;
use App\Models\Certificate;
use Illuminate\Support\Facades\DB;

class DeleteCertificateAction
{
    public function execute(Certificate $certificate): void
    {
        DB::transaction(function () use ($certificate) {
            $certificate = $certificate->freshLockForUpdate();

            $certificate->status = Certificate::STATUS_DELETING;
            $certificate->save();

            DeleteCertificateJob::dispatch($certificate);
        }, 5);
    }
}
