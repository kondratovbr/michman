<?php declare(strict_types=1);

namespace App\Actions\UserSshKeys;

use App\Jobs\UserSshKeys\DeleteUserSshKeyFromServerJob;
use App\Jobs\UserSshKeys\DeleteUserSshKeyJob;
use App\Models\Server;
use App\Models\UserSshKey;
use Illuminate\Support\Facades\Bus;

class FullyDeleteUserSshKeyAction
{
    public function execute(UserSshKey $key): void
    {
        $jobs = [];

        /** @var Server $server */
        foreach ($key->servers as $server) {
            $jobs[] = new DeleteUserSshKeyFromServerJob($key, $server);
        }

        $jobs[] = new DeleteUserSshKeyJob($key);

        Bus::chain($jobs)->dispatch();
    }
}
