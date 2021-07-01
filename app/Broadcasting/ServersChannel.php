<?php declare(strict_types=1);

namespace App\Broadcasting;

use App\Models\Server;
use App\Models\User;

class ServersChannel implements BroadcastingChannelInterface
{
    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    public static function definition(): string
    {
        return 'servers.{server}';
    }

    /**
     * Get the channel's name.
     */
    public static function name(Server $server): string
    {
        return 'servers.' . $server->getKey();
    }
}
