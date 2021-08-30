<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class RestartGunicornScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->exec("systemctl restart {$project->projectName}.socket");
        if ($this->failed())
            throw new ServerScriptException("systemctl command to restart project's Gunicorn socket has failed.");

        $this->exec("systemctl restart {$project->projectName}.service");
        if ($this->failed())
            throw new ServerScriptException("systemctl command to restart project's Gunicorn service has failed.");

        // TODO: CRITICAL! Need to check that Gunicorn has started here. Otherwise a failed deployment may get through as a success.
    }
}
