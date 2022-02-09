<?php declare(strict_types=1);

namespace App\Broadcasting;

use App\Models\Server;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;

class ServerChannel implements BroadcastingChannelInterface
{
    /** Authenticate the user's access to the channel. */
    public function join(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    /** Get the channel's definition string. */
    public static function definition(): string
    {
        return 'servers.{server}';
    }

    /** Get the channel's name. */
    public static function name(Server|int $server): string
    {
        $serverKey = $server instanceof Server
            ? $server->getKey()
            : $server;

        return "servers.{$serverKey}";
    }

    /** Get an instance of Laravel's Channel class corresponding with this broadcasting class. */
    public static function channelInstance(Server|int|null $server): PrivateChannel|null
    {
        if (is_null($server))
            return null;
        
        return new PrivateChannel(static::name($server));
    }
}
