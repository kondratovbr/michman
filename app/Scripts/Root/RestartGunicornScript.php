<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\InteractsWithSystemd;
use phpseclib3\Net\SFTP;

class RestartGunicornScript extends AbstractServerScript
{
    use InteractsWithSystemd;

    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->systemdStopService("{$project->projectName}.socket");

        $this->systemdStopService("{$project->projectName}.service");

        $this->systemdRestartService("{$project->projectName}.socket", true, 30);
    }
}
