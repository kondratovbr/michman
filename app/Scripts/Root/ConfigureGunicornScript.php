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
        $username = $project->serverUsername;
        $domain = $project->domain;
        // Django app module name. Also a directory under a project's root.
        $projectName = explode('/', $project->repo, 2)[1];
        $michmanDir = "/home/{$username}/.michman";
        $configFile = "{$michmanDir}/{$projectName}_gunicorn_config.py";

        $this->init($server, $ssh);

        $data = [
            'domain' => $domain,
            'username' => $username,
            // TODO: CRITICAL! Make sure the projectName is properly handled - it should be a Python "App Module" name - a directory inside the project root. The user should be able to customize it, although we can use the repo name as default - that's how it's usually named.
            'projectName' => $projectName,
        ];

        if (! $this->sendString(
            "/etc/systemd/system/{$projectName}.socket",
            ConfigView::render('gunicorn.socket', $data),
        )) {
            throw new \RuntimeException("Failed to send string to file: /etc/systemd/system/{$projectName}.socket");
        }

        if (! $this->sendString(
            "/etc/systemd/system/{$projectName}.service",
            ConfigView::render('gunicorn.service', $data),
        )) {
            throw new \RuntimeException("Failed to send string to file: /etc/systemd/system/{$projectName}.service");
        }

        if (! $this->sendString(
            $configFile,
            ConfigView::render('gunicorn.default_config', $data),
        )) {
            throw new \RuntimeException("Failed to send string to file: {$configFile}");
        }

        $this->exec("chown {$username}:{$username} {$configFile}");

        $this->exec("systemctl daemon-reload");

        $this->exec("systemctl enable {$projectName}.service");
        $this->exec("systemctl start {$projectName}.service");

        //
    }
}
