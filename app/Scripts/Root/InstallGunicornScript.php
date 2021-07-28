<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Facades\ConfigView;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class InstallGunicornScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $username = $project->serverUsername;
        $domain = $project->domain;
        $projectDir = "/home/{$username}/{$domain}";
        // Django app module name. Also a directory under a project's root.
        $projectName = explode('/', $project->repo, 2)[1];
        $michmanDir = "/home/{$username}/.michman";
        $configFile = "{$michmanDir}/{$projectName}_gunicorn_config.py";

        $this->init($server, $ssh, $username);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty("cd {$projectDir} && source venv/bin/activate");
        $this->read();

        $this->execPty("pip install --ignore-installed gunicorn");
        $this->read();

        $this->execPty("deactivate");
        $this->read();

        $this->disablePty();

        $data = [
            'domain' => $domain,
            'username' => $username,
            // TODO: CRITICAL! Make sure the projectName is properly handled - it should be a Python "App Module" name - a directory inside the project root. The user should be able to customize it, although we can use the repo name as default - that's how it's usually named.
            'projectName' => $projectName,
        ];

        $this->sendString(
            "/etc/systemd/system/{$projectName}.socket",
            ConfigView::render('gunicorn.socket', $data),
        );

        $this->sendString(
            "/etc/systemd/system/{$projectName}.service",
            ConfigView::render('gunicorn.service', $data),
        );

        $this->sendString(
            $configFile,
            ConfigView::render('gunicorn.default_config', $data),
        );

        $this->exec("chown {$username}:{$username} {$configFile}");

        $this->exec("systemctl daemon-reload");

        $this->exec("systemctl enable {$projectName}.service");
        $this->exec("systemctl start {$projectName}.service");

        //
    }
}
