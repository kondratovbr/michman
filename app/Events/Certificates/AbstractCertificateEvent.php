<?php declare(strict_types=1);

namespace App\Events\Certificates;

use App\Broadcasting\ServerChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\Certificate;
use Illuminate\Broadcasting\Channel;

abstract class AbstractCertificateEvent extends AbstractEvent
{
    use Broadcasted;

    public int $certificateKey;
    public int $serverKey;

    public function __construct(Certificate $certificate)
    {
        $this->certificateKey = $certificate->getKey();
        $this->serverKey = $certificate->serverId;
    }

    protected function getChannels(): Channel
    {
        return ServerChannel::channelInstance($this->serverKey);
    }
}
