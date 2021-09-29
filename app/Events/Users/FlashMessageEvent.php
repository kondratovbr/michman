<?php declare(strict_types=1);

namespace App\Events\Users;

use App\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FlashMessageEvent extends AbstractUserEvent implements ShouldBroadcast
{
    // TODO: Replace these with a PHP8.1 enum, if possible.
    public const STYLE_INFO     = 'info';
    public const STYLE_SUCCESS  = 'success';
    public const STYLE_WARNING  = 'warning';
    public const STYLE_DANGER   = 'danger';

    public string $message;
    public string|null $style;

    public function __construct(User $user, string $message, string $style = null)
    {
        parent::__construct($user);

        $this->message = $message;
        $this->style = $style;
    }
}
