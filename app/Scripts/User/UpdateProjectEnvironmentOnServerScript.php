<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;
use RuntimeException;

class UpdateProjectEnvironmentOnServerScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh, $project->serverUsername);

        $envFile = $project->envFilePath;

        if (! $this->sendString($envFile, $project->environment))
            throw new RuntimeException("Failed to send string to file: {$envFile}");

        $this->exec("chmod 0600 {$envFile}");

        if ($this->failed())
            throw new RuntimeException('chmod command failed.');
    }
}
