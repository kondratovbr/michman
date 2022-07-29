<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class TestScript extends AbstractServerScript
{
    public function execute(
        Server $server,
        Project $project,
        SFTP $ssh = null,
    ): void {
        $username = $project->serverUsername;
        $domain = $project->domain;
        $workdir = "/home/$username/$domain";

        $this->init($server, $ssh, $username);

        $this->enablePty();
        $this->setTimeout(60);

        $this->execPty("cd $workdir && source venv/bin/activate && pip --quiet install -r requirements.txt");
        dump($this->read());

        $this->disablePty();
    }
}
