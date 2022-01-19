<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Facades\ConfigView;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithSystemd;
use phpseclib3\Net\SFTP;

class ConfigureGunicornScript extends AbstractServerScript
{
    use InteractsWithSystemd;

    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $projectName = $project->projectName;

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

        $this->systemdReload();
    }
}
