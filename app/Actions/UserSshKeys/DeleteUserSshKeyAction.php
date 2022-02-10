<?php declare(strict_types=1);

namespace App\Actions\UserSshKeys;

use App\Models\UserSshKey;
use Illuminate\Support\Facades\DB;

class DeleteUserSshKeyAction
{
    public function execute(UserSshKey $key): void
    {
        DB::transaction(function () use ($key) {
            $key = $key->freshLockForUpdate();

            $key->servers()->sync([]);
            $key->purge();
        }, 5);
    }
}
