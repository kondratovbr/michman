<?php declare(strict_types=1);

namespace App\Events\UserSshKeys;

use App\Events\Users\AbstractUserEvent;
use App\Models\UserSshKey;

abstract class AbstractUserSshKeyEvent extends AbstractUserEvent
{
    public int $userSshKeyKey;

    public function __construct(UserSshKey $key)
    {
        parent::__construct($key->user);

        $this->userSshKeyKey = $key->getKey();
    }
}
