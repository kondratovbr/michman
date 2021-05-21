<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class CreateSudoUserScript extends AbstractServerScript
{
    public function execute(Server $server, string $username, string $password, SFTP $ssh = null)
    {
        $this->setServer($server);
        $this->setSsh($ssh ?? $server->sftp('root'));

        // Create a new user.
        $this->setTimeout(60 * 5); // 5 min
        $this->exec('useradd --create-home ' . $username);
        $this->exec('echo ' . $username .':' . $password . ' | chpasswd');

        // Add the user to sudo group.
        $this->exec('usermod -aG sudo ' . $username);
    }
}
