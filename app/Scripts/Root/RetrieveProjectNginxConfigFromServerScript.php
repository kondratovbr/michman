<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class RetrieveProjectNginxConfigFromServerScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): string
    {
        $this->init($server, $ssh);

        return $this->getString($project->nginxConfigFilePath);
    }
}
