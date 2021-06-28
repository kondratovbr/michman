<?php declare(strict_types=1);

namespace App\Broadcasting;

use App\Models\Server;
use App\Models\User;

class ServersChannel
{
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user can listen for events broadcasted on this channel.
     */
    public function join(User $user, Server $server): bool
    {
        return $user->is($server->provider->owner);
    }
}
