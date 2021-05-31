<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class VerifyServerAvailabilityScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): bool
    {
        $this->setServer($server);
        $this->setSsh($ssh ?? $server->sftp());

        $this->enablePty();
        $this->read();

        return Str::contains($this->execSudo('sudo whoami'), 'root');
    }
}
