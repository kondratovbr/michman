<?php declare(strict_types=1);

namespace App\Actions\UserSshKeys;

use App\DataTransferObjects\UserSshKeyDto;
use App\Models\User;
use App\Models\UserSshKey;
use phpseclib3\Crypt\PublicKeyLoader;

class StoreUserSshKeyAction
{
    public function execute(UserSshKeyDto $data, User $user): UserSshKey
    {
        /** @var UserSshKey $key */
        $key = $user->userSshKeys()->make($data->toArray());

        $key->publicKey = PublicKeyLoader::loadPublicKey($data->public_key);
        $key->save();

        return $key;
    }
}
