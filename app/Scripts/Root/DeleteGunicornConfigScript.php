<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\InteractsWithSystemd;
use phpseclib3\Net\SFTP;

class DeleteGunicornConfigScript extends AbstractServerScript
{
    use InteractsWithSystemd;

    public function execute(Server $server, Project $project, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh, 'root');

        $projectName = $project->projectName;

        $this->systemdStopService("$projectName.service");
        $this->systemdDisableService("$projectName.service");

        $this->systemdStopService("$projectName.socket");
        $this->systemdDisableService("$projectName.socket");

        $this->exec("rm -rf {$project->gunicornConfigFilePath}");
        $this->exec("rm -rf /etc/systemd/system/$projectName.service");
        $this->exec("rm -rf /etc/systemd/system/$projectName.socket");

        $this->systemdReload();
    }
}
