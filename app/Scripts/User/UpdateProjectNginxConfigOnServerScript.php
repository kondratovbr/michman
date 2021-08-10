<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;
use RuntimeException;

class UpdateProjectNginxConfigOnServerScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh, $project->serverUsername);

        throw new RuntimeException();

        $deployScriptFile = $project->deployScriptFilePath;

        if (! $this->sendString($deployScriptFile, $project->deployScript))
            throw new RuntimeException("Failed to send string to file: {$deployScriptFile}");

        $this->exec("chmod 0744 {$deployScriptFile}");

        if ($this->failed())
            throw new RuntimeException('chmod command failed.');
    }
}
