<?php declare(strict_types=1);

namespace App\Scripts\Sudo;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class VerifyServerAvailabilityScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh): bool
    {
        $this->setServer($server);
        $this->setSsh($ssh ?? $server->sftp());

        return Str::contains($this->execSudo('sudo whoami'), 'root');
    }
}
