<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class CreateSudoUserScript extends AbstractServerScript
{
    public function execute(Server $server, string $username, string $password, SFTP $ssh = null)
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        // Create a new user.
        $this->setTimeout(60 * 5);
        $this->exec("useradd --create-home --shell /bin/bash {$username}");
        $this->exec(
            "echo {$username}:{$password} | chpasswd",
            true,
            false,
            "echo {$username}:PASSWORD | chpasswd",
        );

        // Add the user to sudo group.
        $this->exec("usermod -aG sudo {$username}");

        // Create the necessary directories upfront.
        foreach (['public'] as $dir) {
            $this->exec("mkdir -p /home/{$username}/{$dir} && chown -R {$username}:{$username} /home/{$username}/{$dir}");
        }
    }
}
