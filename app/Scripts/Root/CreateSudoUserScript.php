<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\HandlesUnixUsers;
use phpseclib3\Net\SFTP;

class CreateSudoUserScript extends AbstractServerScript
{
    use HandlesUnixUsers;

    public function execute(Server $server, string $username, string $password, SFTP $ssh = null)
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        $this->createUser($username, true);

        $this->changeUserPassword($username, $password);

        // Create the public directory upfront.
        $dir = "/home/$username/public";
        $this->exec("mkdir -p $dir && chown -R $username:$username $dir && chmod 0755 $dir");
    }
}
