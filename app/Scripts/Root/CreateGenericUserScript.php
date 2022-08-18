<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\HandlesUnixUsers;
use phpseclib3\Net\SFTP;

class CreateGenericUserScript extends AbstractServerScript
{
    use HandlesUnixUsers;

    public function execute(Server $server, string $username, SFTP $ssh = null)
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        $this->createUser($username);

        $homeDir = "/home/$username";
        $michmanDir = "$homeDir/.michman";

        $this->exec("mkdir -p $michmanDir && chown -R $username:$username $homeDir && chmod 0755 $homeDir && chmod 0755 $michmanDir");
    }
}
