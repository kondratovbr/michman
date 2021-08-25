<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Facades\ConfigView;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class ConfigureGunicornScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $username = $project->serverUsername;
        $projectName = $project->projectName;
        $configFile = $project->gunicornConfigFilePath;

        if (! $this->sendString(
            "/etc/systemd/system/{$projectName}.socket",
            ConfigView::render('gunicorn.socket', ['project' => $project]),
        )) {
            throw new ServerScriptException("Failed to send string to file: /etc/systemd/system/{$projectName}.socket");
        }

        if (! $this->sendString(
            "/etc/systemd/system/{$projectName}.service",
            ConfigView::render('gunicorn.service', ['project' => $project]),
        )) {
            throw new ServerScriptException("Failed to send string to file: /etc/systemd/system/{$projectName}.service");
        }

        if (! $this->sendString(
            $configFile,
            $project->gunicornConfig,
        )) {
            throw new ServerScriptException("Failed to send string to file: {$configFile}");
        }

        $this->exec("chown {$username}:{$username} {$configFile}");

        $this->exec("systemctl daemon-reload");
    }
}
