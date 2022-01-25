<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class DeleteUserScript extends AbstractServerScript
{
    public function execute(Server $server, string $username, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $this->exec("deluser --remove-home {$username}");

        $this->exec("delgroup {$username}");
    }
}
