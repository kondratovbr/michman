<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class RestartGunicornScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->exec("systemctl stop {$project->projectName}.socket");

        $this->exec("systemctl stop {$project->projectName}.service");

        $this->exec("systemctl restart {$project->projectName}.socket");

        // TODO: CRITICAL! Need to check that Gunicorn has started here. Otherwise a failed deployment may get through as a success.
    }
}
