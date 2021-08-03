<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Deployment;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;
use RuntimeException;

class PullDeploymentCommitScript extends AbstractServerScript
{
    public function execute(Server $server, Deployment $deployment, SFTP $ssh = null): void
    {
        $project = $deployment->project;

        $this->init($server, $ssh, $project->serverUsername);

        $this->setTimeout(60 * 5);
        // Just in case the user was tinkering with the repo
        $this->exec("git checkout --quiet --force {$deployment->branch}");
        if ($this->failed())
            throw new RuntimeException("git checkout command has failed.");

        // Fetch and merge replace pull,
        // which is done to ensure the exact specific commit is being deployed.

        $this->exec("git fetch --quiet --atomic --force --theirs origin {$deployment->branch}");
        if ($this->failed())
            throw new RuntimeException("git fetch command has failed.");

        $this->exec("git merge {$deployment->commit}");
        if ($this->failed())
            throw new RuntimeException("git merge command has failed.");
    }
}
