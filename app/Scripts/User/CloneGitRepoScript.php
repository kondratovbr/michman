<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Services\VcsProviderInterface;
use phpseclib3\Net\SFTP;

class CloneGitRepoScript extends AbstractServerScript
{
    public function execute(
        Server $server,
        Project $project,
        SFTP $ssh = null,
        VcsProviderInterface $vcs = null,
    ): void {
        $vcs ??= $project->vcsProvider->api();

        $username = $project->serverUsername;
        $repoSshString = $project->vcsProvider->api()::getFullSshString($project->repo);
        $domain = $project->domain;
        $sshHostKey = $vcs->getSshHostKey();

        $homedir = "/home/{$username}";
        $knownHostsFile = "{$homedir}/.ssh/known_hosts";
        $projectDir = "{$homedir}/{$domain}";
        $sshKeyName = $project->useDeployKey ? $project->deploySshKey->name : 'id_ed25519';
        $sshKeyFile = "{$homedir}/.ssh/{$sshKeyName}";

        $this->init($server, $ssh, $username);

        // Create the known_hosts file if it doesn't exist.
        $this->exec("touch {$knownHostsFile}");
        $this->exec("chmod 0644 {$knownHostsFile}");

        // Use grep to check if the VCS's SSH host key is already added to the known_hosts file
        // and add if it isn't.
        $this->exec("grep -qxF '{$sshHostKey}' {$knownHostsFile} || echo '{$sshHostKey}' >> {$knownHostsFile}");

        // This script is for the initial repo cloning, so we can safely delete the directory.
        // It may already exist if the cloning was already tried before and failed,
        // or if the user was tinkering on the server manually.
        $this->exec("rm -rf {$projectDir}");

        // TODO: IMPORTANT! Figure out file permissions here. Project files shouldn't be modifiable and directories
        //       shouldn't be writable by other users. Same thing when we pull changes during deployment.
        $this->exec(
            "git -c core.sshCommand=\"ssh -i {$sshKeyFile}\" clone --single-branch --branch {$project->branch} --depth 1 {$repoSshString} {$projectDir}"
        );
    }
}
