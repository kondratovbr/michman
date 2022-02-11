<?php declare(strict_types=1);

namespace App\Events\VcsProviders;

use App\Broadcasting\UserChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\VcsProvider;
use Illuminate\Broadcasting\Channel;

abstract class AbstractVcsProviderEvent extends AbstractEvent
{
    use Broadcasted;

    public int $vcsProviderKey;
    public int $userKey;

    public function __construct(VcsProvider $vcsProvider)
    {
        $this->vcsProviderKey = $vcsProvider->getKey();
        $this->userKey = $vcsProvider->userId;
    }

    protected function getChannels(): Channel|array|null
    {
        return UserChannel::channelInstance($this->userKey);
    }
}
