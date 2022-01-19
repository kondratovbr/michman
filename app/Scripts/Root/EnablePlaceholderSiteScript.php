<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class EnablePlaceholderSiteScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $available = "/etc/nginx/sites-available";
        $enabled = "/etc/nginx/sites-enabled";
        $file = "michman_placeholder.conf";

        $this->exec("ln -sf {$available}/{$file} {$enabled}/{$file}");
    }
}
