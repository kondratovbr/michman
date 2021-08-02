<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class RetrieveProjectDeployScriptFromServerScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): string|null
    {
        $this->init($server, $ssh, $project->serverUsername);

        return $this->getString($project->deployScriptFilePath);
    }
}
