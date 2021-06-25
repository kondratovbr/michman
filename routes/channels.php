<?php declare(strict_types=1);

use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('users.{userKey}', function (User $user, $userKey) {
    return $user->getKey() == $userKey;
});

Broadcast::channel('servers.{server}', function (User $user, Server $server) {
    return $user->is($server->provider->owner);
});
