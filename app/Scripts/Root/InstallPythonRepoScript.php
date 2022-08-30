<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\InteractsWithApt;
use phpseclib3\Net\SFTP;

class InstallPythonRepoScript extends AbstractServerScript
{
    use InteractsWithApt;

    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        // Community repo with literally all versions of Python pre-built.
        // https://askubuntu.com/a/682875
        $this->aptAddRepo('ppa:deadsnakes/ppa');
    }
}
