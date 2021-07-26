<?php declare(strict_types=1);

namespace App\Scripts\Worker;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class CloneGitRepoScript extends AbstractServerScript
{
    public function execute(
        Server $server,
        string $repoSshString,
        string $domain,
        string $sshHostKey,
        SFTP $ssh = null,
    ): void {
        $user = (string) config('servers.worker_user');
        $homedir = "/home/{$user}";
        $knownHostsFile = "{$homedir}/.ssh/known_hosts";
        $projectDir = "{$homedir}/{$domain}";

        $this->init($server, $ssh, $user);

        // Create the known_hosts file if it doesn't exist.
        $this->exec("touch {$knownHostsFile}");
        $this->exec("chmod 0644 {$knownHostsFile}");

        // Use grep to check if the VCS's SSH host key is already added to the known_hosts file
        // and add if it doesn't.
        $this->exec("grep -qxF '{$sshHostKey}' {$knownHostsFile} || echo '{$sshHostKey}' >> {$knownHostsFile}");

        // TODO:CRITICAL! CONTINUE. Implement. Need to figure out how to check that the repo is available from the server and show the user some feedback about it. Probably in a separate script before this one.

        $this->exec("git clone --single-branch --branch main --depth 1 {$repoSshString} {$projectDir}");

        // code 128
        // WARNING: REMOTE HOST IDENTIFICATION HAS CHANGED!

        //
    }
}
