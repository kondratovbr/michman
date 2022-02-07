<?php declare(strict_types=1);

namespace App\Actions\Users;

use App\Jobs\Users\DeleteUserJob;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// TODO: Cover with tests.

class DeleteUserAction
{
    public function execute(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->freshLockForUpdate();

            DeleteUserJob::dispatch($user);

            $user->delete();
        }, 5);
    }


}
