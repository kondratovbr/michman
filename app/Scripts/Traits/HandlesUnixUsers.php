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
    protected function createUser(
        string $username,
        bool $sudo = false,
        bool $nologin = false,
    ): void {
        $shell = $nologin ? '/usr/sbin/nologin' : '/bin/bash';

        // useradd fails if the user already exists, so we check for it.
        $this->exec("id -u $username", throw: false);
        if ($this->failed())
            $this->exec("useradd --create-home --shell $shell $username");

        $this->exec("chmod 0755 /home/$username");

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
