<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class UpdateProjectGunicornConfigOnServerScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh, $project->serverUsername);

        $configFile = $project->gunicornConfigFilePath;

        if (! $this->sendString($configFile, $project->gunicornConfig)) {
            throw new ServerScriptException("Failed to send string to file: {$configFile}");
        }
    }
}
