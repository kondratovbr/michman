<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class EnablePlaceholderSiteScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $available = "/etc/nginx/sites-available";
        $enabled = "/etc/nginx/sites-enabled";
        $file = "{$project->projectName}_placeholder.conf";

        if ($this->exec("ln -sf {$available}/{$file} {$enabled}/{$file}") === false)
            throw new ServerScriptException('Failed to create a symlink to Nginx placeholder config.');
    }
}
