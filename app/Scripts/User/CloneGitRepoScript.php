<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;
use RuntimeException;

class CloneGitRepoScript extends AbstractServerScript
{
    public function execute(
        Server $server,
        Project $project,
        SFTP $ssh = null,
    ): void {
        $vcs = $project->vcsProvider->api();

        $username = $project->serverUsername;
        $repoSshString = $project->vcsProvider->api()::getFullSshString($project->repo);
        $domain = $project->domain;
        $sshHostKey = $vcs->getSshHostKey();

        $homedir = "/home/{$username}";
        $knownHostsFile = "{$homedir}/.ssh/known_hosts";
        $projectDir = "{$homedir}/{$domain}";

        $this->init($server, $ssh, $username);

        // Create the known_hosts file if it doesn't exist.
        $this->exec("touch {$knownHostsFile}");
        $this->exec("chmod 0644 {$knownHostsFile}");

        // Use grep to check if the VCS's SSH host key is already added to the known_hosts file
        // and add if it isn't.
        $this->exec("grep -qxF '{$sshHostKey}' {$knownHostsFile} || echo '{$sshHostKey}' >> {$knownHostsFile}");

        $this->exec("git clone --single-branch --branch main --depth 1 {$repoSshString} {$projectDir}");

        if ($this->getExitStatus() !== 0)
            throw new RuntimeException('Cloning the project\'s git repo failed.');
    }
}
