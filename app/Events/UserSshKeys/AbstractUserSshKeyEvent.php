<?php declare(strict_types=1);

namespace App\Events\UserSshKeys;

use App\Broadcasting\UserChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\UserSshKey;
use Illuminate\Broadcasting\Channel;

abstract class AbstractUserSshKeyEvent extends AbstractEvent
{
    use Broadcasted;

    public int $userSshKeyKey;
    public int $userKey;

    public function __construct(UserSshKey $sshKey)
    {
        $this->userSshKeyKey = $sshKey->getKey();
        $this->userKey = $sshKey->userId;
    }

    protected function getChannels(): Channel
    {
        return UserChannel::channelInstance($this->userKey);
    }
}
