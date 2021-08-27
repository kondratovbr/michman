<?php declare(strict_types=1);

namespace App\Actions\UserSshKeys;

use App\Models\UserSshKey;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class DeleteUserSshKeyAction
{
    public function execute(UserSshKey $key): void
    {
        DB::transaction(function () use ($key) {
            $key = $key->freshLockForUpdate();

            $key->servers()->sync([]);
            $key->delete();
        }, 5);
    }
}
