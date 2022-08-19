<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Exception\ConnectionClosedException;
use phpseclib3\Net\SFTP;

class RebootServerScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        try {
            $this->exec('reboot');
        } catch (ConnectionClosedException) {
            // We're rebooting the server, so this exception is almost guaranteed to be thrown.
        }
    }
}
