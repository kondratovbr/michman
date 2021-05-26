<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Support\Str;
use Composer\Semver\Comparator;
use phpseclib3\Net\SFTP;

class VerifyServerIsSuitableScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): bool
    {
        $this->setServer($server);
        $this->setSsh($ssh ?? $server->sftp('root'));

        // TODO: IMPORTANT! Must test this whole thing with other providers. Only tested on DigitalOcean so far. Add some random generic VPSs as well.

        // Server is running Ubuntu.
        if (! Str::contains($this->exec('uname -v'), ['ubuntu', 'Ubuntu']))
            return false;

        // The version of Ubuntu is relatively recent - at least 16.04.
        $version = $this->exec('lsb_release -sr');
        if ($this->getExitStatus() || Comparator::lessThan($version, '16.04'))
            return false;

        // We have root access at the moment.
        if (! Str::contains($this->exec('whoami'), 'root'))
            return false;

        // apt-get is installed and accessible.
        $this->exec('apt-get -v');
        if ($this->getExitStatus())
            return false;

        return true;
    }
}
