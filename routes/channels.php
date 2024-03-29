<?php declare(strict_types=1);

use App\Broadcasting\ProjectChannel;
use App\Broadcasting\ServerChannel;
use App\Broadcasting\UserChannel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel(UserChannel::definition(), UserChannel::class);

Broadcast::channel(ServerChannel::definition(), ServerChannel::class);

Broadcast::channel(ProjectChannel::definition(), ProjectChannel::class);
