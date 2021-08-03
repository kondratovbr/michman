<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Deployment;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class RunDeploymentScriptScript extends AbstractServerScript
{
    public function execute(Server $server, Deployment $deployment, SFTP $ssh = null): void
    {
        $project = $deployment->project;

        $this->init($server, $ssh, $project->serverUsername);

        // TODO: CRITICAL! CONTINUE.

        //
    }
}
