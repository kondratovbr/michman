<?php declare(strict_types=1);

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;

class UserChannel implements BroadcastingChannelInterface
{
    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, User $subject): bool
    {
        return $user->is($subject);
    }

    /**
     * Get the channel's definition string.
     */
    public static function definition(): string
    {
        return 'user.{user}';
    }

    /**
     * Get the channel's name.
     */
    public static function name(User|int $user): string
    {
        $userKey = $user instanceof User
            ? $user->getKey()
            : $user;

        return "user.{$userKey}";
    }

    /**
     * Get an instance of Laravel's Channel class corresponding with this broadcasting class.
     */
    public static function channelInstance(User|int $user): PrivateChannel
    {
        return new PrivateChannel(static::name($user));
    }
}
