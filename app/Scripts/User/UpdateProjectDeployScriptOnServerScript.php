<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class UpdateProjectDeployScriptOnServerScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh, $project->serverUsername);

        $deployScriptFile = $project->deployScriptFilePath;

        if (! $this->sendString($deployScriptFile, $project->deployScript))
            throw new ServerScriptException("Failed to send string to file: {$deployScriptFile}");

        $this->exec("chmod 0744 {$deployScriptFile}");

        if ($this->failed())
            throw new ServerScriptException('chmod command failed.');
    }
}
