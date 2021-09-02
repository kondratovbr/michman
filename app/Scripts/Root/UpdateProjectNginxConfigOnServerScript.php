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
    public function execute(Server $server, Project $project, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $this->exec("mkdir -p /etc/nginx/sites-available && mkdir -p /etc/nginx/sites-enabled");

        if (! $this->sendString(
            $project->nginxConfigFilePath,
            ConfigView::render(
                $server->getCertificatesFor($project)->isEmpty() ? 'nginx.server' : 'nginx.server_ssl',
                [
                    'server' => $server,
                    'project' => $project,
                ])
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
