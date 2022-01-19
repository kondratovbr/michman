<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class DeleteProjectFilesScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $userSsh = null): void
    {
        $this->init($server, $userSsh, $project->serverUsername);

        $this->exec("rm -rf {$project->projectDir}");

        $this->exec("rm -rf {$project->deployScriptFilePath}");
    }
}
