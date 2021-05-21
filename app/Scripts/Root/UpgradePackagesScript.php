<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class UpgradePackagesScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->setServer($server);
        $this->setSsh($ssh ?? $server->sftp('root'));

        // TODO: CRITICAL! CONTINUE! This thing doesn't really work. Xdebug stops it for some reason, need to try it without.

        /*
         * TODO: IMPORTANT! Make sure to handle a situation when an apt-get gets interrupted by something (like an outage of sorts) so
         *       'dpkg was interrupted, you must manually run 'dpkg --configure -a' to correct the problem.'
         *       message shows the next time.
         *       Notify myself on an emergency channel since this will probably require some manual fixing.
         *       Also, there's another possible error:
         *       "Could not get lock..."
         *       Need to handle it as well.
         */

        $this->enablePty();
        $this->setTimeout(60 * 15); // 15 min
        $this->execPty('apt-get update -y');
        $this->read();
        $this->execPty('apt-get upgrade --with-new-pkgs -y');
        $this->read();
        $this->execPty('apt-get install -y ufw git curl gnupg gzip unattended-upgrades');
        $this->read();
        $this->disablePty();
    }
}
