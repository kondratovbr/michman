<?php declare(strict_types=1);

namespace App\Events\Users;

use App\Broadcasting\UserChannel;
use App\Events\AbstractEvent;
use App\Models\User;
use Illuminate\Broadcasting\Channel;

abstract class AbstractUserEvent extends AbstractEvent
{
    public int $userKey;

    public function __construct(User $user)
    {
        $this->userKey = $user->getKey();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel|array
    {
        return UserChannel::channelInstance($this->userKey);
    }
}
