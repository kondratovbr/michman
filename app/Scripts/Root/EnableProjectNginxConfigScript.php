<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class EnableProjectNginxConfigScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $available = "/etc/nginx/sites-available";
        $enabled = "/etc/nginx/sites-enabled";
        $file = "{$project->projectName}.conf";

        $this->exec("ln -sf {$available}/{$file} {$enabled}/{$file}");
    }
}
