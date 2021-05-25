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

        /*
         * TODO: CRITICAL! Make sure to handle a situation when an apt-get gets interrupted by something (like an outage of sorts) so
         *       'dpkg was interrupted, you must manually run 'dpkg --configure -a' to correct the problem.'
         *       message shows the next time.
         *       Notify myself on an emergency channel since this will probably require some manual fixing.
         *       Or maybe just destroy and recreate the server and try again if it was happening during the initial phase.
         *       Also, there's another possible error:
         *       "Could not get lock..."
         *       Need to handle it as well.
         *       Note: there are some other similar issues, see
         *           https://itsfoss.com/could-not-get-lock-error/
         *           https://pingvinus.ru/note/dpkg-lock
         */

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min
        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get update -y');
        $this->read();
        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get upgrade --with-new-pkgs -y');
        $this->read();
        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get install -y ufw git curl gnupg gzip unattended-upgrades');
        $this->read();
        $this->disablePty();
    }
}
