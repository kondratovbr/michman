<?php declare(strict_types=1);

namespace App\Scripts\Worker;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class CloneGitRepoScript extends AbstractServerScript
{
    public function execute(Server $server, string $repoSshString, string $domain, SFTP $ssh = null): void
    {
        $user = (string) config('servers.worker_user');

        $this->init($server, $ssh, $user);

        // TODO:CRITICAL! CONTINUE. Implement. Need to figure out how to check that the repo is available from the server and show the user some feedback about it. Probably in a separate script before this one.

        $this->exec("cd /home/{$user} && clone --single-branch --branch main --depth 1 {$repoSshString} {$domain}");

        //
    }
}
