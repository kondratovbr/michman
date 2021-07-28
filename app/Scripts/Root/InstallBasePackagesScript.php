<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class InstallBasePackagesScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        /*
         * TODO: CRITICAL! Make sure to handle a situation when an apt-get gets interrupted by something (like an outage of sorts) so
         *       'dpkg was interrupted, you must manually run 'dpkg --configure -a --force-confold --force-confdef' to correct the problem.'
         *       message shows the next time.
         *       (The "force" parameters instruct dpkg on what to do with conflicting config files -
         *       this combination should update the ones that weren't modified by the user and keep the ones that were.
         *       Notify myself on an emergency channel since this will probably require some manual fixing.
         *       Or maybe just destroy and recreate the server and try again if it was happening during the initial phase.
         *       Also, there's another possible error:
         *       "Could not get lock..."
         *       Need to handle it as well.
         *       Note: there are some other similar issues, see
         *           https://itsfoss.com/could-not-get-lock-error/
         *           https://pingvinus.ru/note/dpkg-lock
         *       Note: I use apt-get in other scripts as well - search for all of them and make sure they work too.
         *       See:
         *       https://askubuntu.com/questions/104899/make-apt-get-or-aptitude-run-with-y-but-not-prompt-for-replacement-of-configu
         *       https://askubuntu.com/questions/163200/e-dpkg-was-interrupted-run-sudo-dpkg-configure-a
         */

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty(
            'DEBIAN_FRONTEND=noninteractive apt-get install -y '
            . implode(' ', [
                'ufw',
                'git',
                'curl',
                'gnupg',
                'gzip',
                'unattended-upgrades',
                'supervisor',
            ])
        );
        $this->read();

        $this->disablePty();
    }
}
