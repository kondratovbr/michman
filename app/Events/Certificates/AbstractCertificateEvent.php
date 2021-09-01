<?php declare(strict_types=1);

namespace App\Events\Certificates;

use App\Events\Servers\AbstractServerEvent;
use App\Models\Certificate;

abstract class AbstractCertificateEvent extends AbstractServerEvent
{
    protected int $certificateKey;

    public function __construct(Certificate $certificate)
    {
        parent::__construct($certificate->server);

        $this->certificateKey = $certificate->getKey();
    }
}
