<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Facades\ConfigView;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class ConfigureGunicornScript extends AbstractServerScript
{
    /*
     * TODO: CRITICAL! CONTINUE. This kinda works but not really - to actually run the service Python should be able to load the environment,
     *       which means .env should exist and the user should have had a chance to change it.
     *       So, running the Gunicorn service should be a part of deployment, not repo installation. Repo should be finished at venv and requirements.txt.
     */

    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $username = $project->serverUsername;
        $projectName = $project->projectName;
        $michmanDir = "/home/{$username}/.michman";
        $configFile = "{$michmanDir}/{$projectName}_gunicorn_config.py";

        if (! $this->sendString(
            "/etc/systemd/system/{$projectName}.socket",
            ConfigView::render('gunicorn.socket', ['project' => $project]),
        )) {
            throw new \RuntimeException("Failed to send string to file: /etc/systemd/system/{$projectName}.socket");
        }

        if (! $this->sendString(
            "/etc/systemd/system/{$projectName}.service",
            ConfigView::render('gunicorn.service', ['project' => $project]),
        )) {
            throw new \RuntimeException("Failed to send string to file: /etc/systemd/system/{$projectName}.service");
        }

        if (! $this->sendString(
            $configFile,
            $project->gunicornConfig,
        )) {
            throw new \RuntimeException("Failed to send string to file: {$configFile}");
        }

        $this->exec("chown {$username}:{$username} {$configFile}");

        $this->exec("systemctl daemon-reload");
    }
}
