<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class UpdateProjectEnvironmentOnServerScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh, $project->serverUsername);

        $envFile = $project->envFilePath;

        if (! $this->sendString($envFile, $project->environment))
            throw new ServerScriptException("Failed to send string to file: {$envFile}");

        $this->exec("chmod 0600 {$envFile}");

        if ($this->failed())
            throw new ServerScriptException('chmod command failed.');
    }
}
