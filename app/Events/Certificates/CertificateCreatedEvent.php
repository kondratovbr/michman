<?php declare(strict_types=1);

namespace App\Events\Certificates;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CertificateCreatedEvent extends AbstractCertificateEvent implements ShouldBroadcast
{
    //
}
