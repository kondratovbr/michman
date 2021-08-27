<?php declare(strict_types=1);

namespace App\Actions\UserSshKeys;

use App\Jobs\UserSshKeys\UploadUserSshKeyToServerJob;
use App\Models\Server;
use App\Models\UserSshKey;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class AddUserSshKeyToOwnerServersAction
{
    public function execute(UserSshKey $key): void
    {
        DB::transaction(function () use ($key) {
            $key = $key->freshLockForUpdate();
            $user = $key->user;
            $userServers = $user->servers;

            $jobs = [];

            /** @var Server $server */
            foreach ($userServers as $server) {
                if (! $key->servers->contains($server)) {
                    $key->servers()->attach($server);
                    $jobs[] = new UploadUserSshKeyToServerJob($key, $server);
                }
            }

            Bus::chain($jobs)->dispatch();
        }, 5);
    }
}
