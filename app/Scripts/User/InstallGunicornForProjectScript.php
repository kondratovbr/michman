<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Facades\ConfigView;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;
use RuntimeException;

class InstallGunicornForProjectScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $username = $project->serverUsername;
        $domain = $project->domain;
        $workdir = "/home/{$username}/{$domain}";
        $michmanDir = "/home/{$username}/.michman";
        $package = $project->package;
        $configFile = "{$michmanDir}/{$package}_gunicorn_config.py";

        $this->init($server, $ssh, $username);

        $this->enablePty();
        $this->setTimeout(60 * 5); // 5 min

        $this->execPty("cd {$workdir} && source venv/bin/activate && pip --quiet install --ignore-installed gunicorn && deactivate");
        $this->read();

        if ($this->failed())
            throw new RuntimeException('"pip install gunicorn" command has failed.');

        $this->disablePty();

        if (! $this->sendString(
            $configFile,
            ConfigView::render('gunicorn.default_config', $data),
        )) {
            throw new \RuntimeException("Failed to send string to file: {$configFile}");
        }

        $this->exec("chown {$username}:{$username} {$configFile}");
    }
}
