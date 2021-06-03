<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Support\Str;
use phpseclib3\Net\SFTP;
use RuntimeException;

class InstallNginxScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        /*
         * TODO: IMPORTANT! Figure out how to verify that Nginx is installed and is managed by systemd.
         *       Also, figure out what to do if something fails here, like in all other scripts.
         */

        /*
         * TODO: CRITICAL! Don't forget to figure out SSL setup.
         */

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get update -y');
        $this->read();

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get install -y nginx');
        $this->read();

        $this->disablePty();

        // TODO: CRITICAL! Don't forget to also send the config files and restart nginx afterwards.

        // Create a nologin system user to run Nginx workers as configure in nginx.conf.
        $this->exec('useradd -r -s /usr/sbin/nologin nginx');

        // TODO: IMPORTANT! Have a Michman-branded static page showing up when there's no project set up on a server. See how Forge does it.

        // Restart Nginx to load new config.
        $this->exec('systemctl restart nginx');

        // Wait a bit for Nginx to be started by systemd.
        $this->setTimeout(60);
        $this->exec('sleep 30');

        // Verify that Nginx has started.
        $output = $this->exec('systemctl status nginx');
        if (
            ! Str::contains(Str::lower($output), 'active (running)')
            || $this->getExitStatus() !== 0
        ) {
            throw new RuntimeException('Nginx failed to start.');
        }
    }
}
