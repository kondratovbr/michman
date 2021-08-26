<?php declare(strict_types=1);

namespace App\Actions\UserSshKeys;

use App\DataTransferObjects\UserSshKeyData;
use App\Models\User;
use App\Models\UserSshKey;

class StoreUserSshKeyAction
{
    public function execute(UserSshKeyData $data, User $user): UserSshKey
    {
        return $user->userSshKeys()->create($data->toArray());
    }
}
