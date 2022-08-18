<?php declare(strict_types=1);

namespace App\Scripts\Traits;

use App\Scripts\AbstractServerScript;

/**
 * Trait for managing Unix users on servers.
 *
 * @mixin AbstractServerScript
 */
trait HandlesUnixUsers
{
    protected function createUser(string $username, bool $sudo = false): void
    {
        // useradd fails if the user already exists, so we check for it.
        if (! $this->exec("id -u $username", throw: false))
            $this->exec("useradd --create-home --shell /bin/bash $username");

        if ($sudo)
            $this->makeUserSudo($username);
    }

    protected function changeUserPassword(string $username, string $password): void
    {
        $this->exec(
            "echo $username:$password | chpasswd",
            true,
            false,
            "echo $username:PASSWORD | chpasswd",
        );
    }

    protected function makeUserSudo(string $username): void
    {
        // Add the user to sudo group.
        $this->exec("usermod -aG sudo $username");
    }
}
