<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;
use RuntimeException;

class CreateGenericUserScript extends AbstractServerScript
{
    public function execute(Server $server, string $username, SFTP $ssh = null)
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        $michmanDir = "/home/{$username}/.michman";

        $this->setTimeout(60);

        $this->exec("useradd --create-home --shell /bin/bash {$username}");

        if ($this->failed())
            throw new RuntimeException('useradd has failed.');

        $this->exec("mkdir -p {$michmanDir} && chown {$username}:{$username} {$michmanDir} && chmod 0755 {$michmanDir}");
    }
}
