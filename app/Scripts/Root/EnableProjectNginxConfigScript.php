<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;
use RuntimeException;

class EnableProjectNginxConfigScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $available = "/etc/nginx/sites-available";
        $enabled = "/etc/nginx/sites-enabled";
        $file = "{$project->projectName}.conf";
        $placeholder = "{$project->projectName}_placeholder.conf";

        if ($this->exec("ln -sf {$available}/{$file} {$enabled}/{$file}") === false)
            throw new RuntimeException('Failed to create a symlink to Nginx placeholder config.');

        if ($this->exec("rm -rf {$enabled}/{$placeholder}"))
            throw new RuntimeException('Failed to remove a symlink for the placeholder page Nginx config.');
    }
}
