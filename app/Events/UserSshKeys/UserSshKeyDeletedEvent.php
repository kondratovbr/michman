<?php declare(strict_types=1);

namespace App\Events\UserSshKeys;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserSshKeyDeletedEvent extends AbstractUserSshKeyEvent implements ShouldBroadcast
{
    //
}
