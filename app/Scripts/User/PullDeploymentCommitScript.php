<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Deployment;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class PullDeploymentCommitScript extends AbstractServerScript
{
    public function execute(Server $server, Deployment $deployment, SFTP $ssh = null): void
    {
        $project = $deployment->project;

        $this->init($server, $ssh, $project->serverUsername);

        $homedir = "/home/{$project->serverUsername}";
        $sshKeyName = $project->useDeployKey ? $project->deploySshKey->name : 'id_ed25519';
        $sshKeyFile = "{$homedir}/.ssh/{$sshKeyName}";

        $this->setTimeout(60 * 5);
        // Just in case the user was tinkering with the repo
        $this->exec("cd {$project->projectDir} && git checkout --quiet --force {$deployment->branch}");

        // Fetch and merge replace pull, which is done to ensure the exact specific commit is being deployed.

        $this->exec(
            "cd {$project->projectDir} && git -c core.sshCommand=\"ssh -i $sshKeyFile\" fetch --quiet --force origin {$deployment->branch}"
        );

        $this->exec("cd {$project->projectDir} && git merge --quiet -X theirs {$deployment->commit}");
    }
}
