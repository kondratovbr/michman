<?php declare(strict_types=1);

namespace App\Events\UserSshKeys;

use App\Broadcasting\UserChannel;
use App\Events\AbstractEvent;
use App\Models\UserSshKey;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserSshKeyDeletedEvent extends AbstractEvent implements ShouldBroadcast
{
    public int $userSshKeyKey;
    public int $userKey;

    public function __construct(UserSshKey $key)
    {
        $this->userSshKeyKey = $key->getKey();
        $this->userKey = $key->user->getKey();
    }

    public function broadcastOn(): Channel|array
    {
        return UserChannel::channelInstance($this->userKey);
    }
}
