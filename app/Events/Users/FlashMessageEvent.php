<?php declare(strict_types=1);

namespace App\Events\Users;

use App\Broadcasting\UserChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/*
 * TODO: IMPORTANT! I'm using my standard broadcasting logic to broadcast these events.
 *       Is there a significant delay because of that in the real world?
 *       Can it be improved or reworked entirely?
 */

class FlashMessageEvent extends AbstractEvent implements ShouldBroadcast
{
    use Broadcasted;

    // TODO: Replace these with a PHP8.1 enum, if possible.
    public const STYLE_INFO     = 'info';
    public const STYLE_SUCCESS  = 'success';
    public const STYLE_WARNING  = 'warning';
    public const STYLE_DANGER   = 'danger';

    public string $message;
    public string|null $style;
    protected int $userKey;

    public function __construct(User $user, string $message, string $style = null)
    {
        $this->userKey = $user->getKey();
        $this->message = $message;
        $this->style = $style;
    }

    protected function getChannels(): Channel
    {
        return UserChannel::channelInstance($this->userKey);
    }
}
