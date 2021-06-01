<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class InstallGunicornScript extends AbstractServerScript
{
    /*
     * TODO: CRITICAL! Test Gunicorn installation and configuration on Ubuntu 18!
     *       It seems like it has a seriously outdated (Python 2) version of Guniorn in the default repositories.
     *       Read the docs: https://docs.gunicorn.org/en/stable/install.html
     */

    // TODO: Make sure to properly configure number of Gunicorn workers: https://docs.gunicorn.org/en/stable/design.html

    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        // TODO: CRITICAL! Implement.

        //
    }
}
