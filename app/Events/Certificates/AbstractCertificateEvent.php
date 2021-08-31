<?php declare(strict_types=1);

namespace App\Events\Certificates;

use App\Events\Projects\AbstractProjectEvent;
use App\Models\Certificate;

abstract class AbstractCertificateEvent extends AbstractProjectEvent
{
    protected int $certificateKey;

    public function __construct(Certificate $certificate)
    {
        parent::__construct($certificate->project);

        $this->certificateKey = $certificate->getKey();
    }
}
