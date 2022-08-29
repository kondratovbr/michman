<?php declare(strict_types=1);

namespace App\Scripts\Traits;

use App\Scripts\AbstractServerScript;
use App\Support\Arr;

/*
 * TODO: IMPORTANT! Make sure to handle a situation when an apt-get gets interrupted by something (like an outage of sorts) so
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

/**
 * Trait for using apt-get on the servers.
 *
 * @mixin AbstractServerScript
 */
trait InteractsWithApt
{
    /*
     * The timeout is here to kill the script if apt-get gets stuck at some point -
     * it's a long-running thing and may get stuck due to external factors.
     */

    private int $timeout = 60 * 30; // 30 min

    protected function aptPrepare(): string
    {
        // This is a workaround for the recent DigitalOcean bug in their Ubuntu 22.04 image.
        // The repo listed in that file comes with no GPG key, so apt refuses to do anything.
        // TODO: Check if the bug is still present and if we can have a different workaround.
        return $this->exec("rm -f /etc/apt/sources.list.d/digitalocean-agent.list");
    }

    protected function aptUpdate(): string
    {
        return $this->execLong(
            'DEBIAN_FRONTEND=noninteractive apt-get update -y',
            $this->timeout,
        );
    }

    protected function aptInstall(array|string $packages): string
    {
        return $this->execLong(
            'DEBIAN_FRONTEND=noninteractive apt-get install -y ' .
            implode(' ', Arr::wrap($packages)),
            $this->timeout,
        );
    }

    protected function aptUpgrade(): string
    {
        return $this->execLong(
            'DEBIAN_FRONTEND=noninteractive apt-get upgrade --with-new-pkgs -y',
            $this->timeout,
        );
    }
}
