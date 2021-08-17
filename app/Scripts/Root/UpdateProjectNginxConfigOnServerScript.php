<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class UpdateProjectNginxConfigOnServerScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $configFile = "/etc/nginx/sites-available/{$project->projectName}.conf";

        if (! $this->sendString($configFile, $project->nginxConfig))
            throw new ServerScriptException("Failed to send string to file: {$configFile}");
    }
}
