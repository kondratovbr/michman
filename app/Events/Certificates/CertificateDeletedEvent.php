<?php declare(strict_types=1);

namespace App\Events\Certificates;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CertificateDeletedEvent extends AbstractCertificateEvent implements ShouldBroadcast
{
    //
}
