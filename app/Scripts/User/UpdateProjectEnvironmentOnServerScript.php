<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class UpdateProjectEnvironmentOnServerScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $userSsh = null): void
    {
        $this->init($server, $userSsh, $project->serverUsername);

        $envFile = $project->envFilePath;

        if (! $this->sendString($envFile, $project->environment))
            throw new ServerScriptException("Failed to send string to file: {$envFile}");

        $this->exec("chmod 0600 {$envFile}");
    }
}
