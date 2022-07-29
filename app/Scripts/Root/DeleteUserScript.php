<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class DeleteUserScript extends AbstractServerScript
{
    public function execute(Server $server, string $username, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $users = $this->exec("cut -d: -f1 /etc/passwd");

        if (Str::contains($users, $username))
            $this->exec("userdel -r -f $username");

        $this->exec("rm -rf /home/$username");
    }
}
