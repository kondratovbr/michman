<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Facades\ConfigView;
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

        if (! $this->sendString(
            $project->nginxConfigFilePath,
            ConfigView::render(
                $project->hasSsl() ? 'nginx.server_ssl' : 'nginx.server',
                ['project' => $project])
            )
        ) {
            throw new ServerScriptException("Failed to send string to file: {$project->nginxConfigFilePath}");
        }

        if (! $this->sendString(
            $project->userNginxConfigFilePath,
            $project->nginxConfig)
        ) {
            throw new ServerScriptException("Failed to send string to file: {$project->userNginxConfigFilePath}");
        }

        $this->exec("chown {$project->serverUsername}:{$project->serverUsername} {$project->userNginxConfigFilePath}");

        if ($this->failed())
            throw new ServerScriptException("Failed to chown file {$project->userNginxConfigFilePath} to user {$project->serverUsername}");
    }
}
