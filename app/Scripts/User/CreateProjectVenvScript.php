<?php declare(strict_types=1);

namespace App\Scripts\User;


use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;
use RuntimeException;

class CreateProjectVenvScript extends AbstractServerScript
{
    // TODO: CRITICAL! Make sure to implement some feedback for the user in case of some problems here. See the notification system.

    public function execute(
        Server $server,
        Project $project,
        bool $installDependencies,
        SFTP $ssh = null,
    ): void {
        $username = $project->serverUsername;
        $workdir = "/home/{$username}/{$project->domain}";

        $this->init($server, $ssh, $project->serverUsername);

        $this->exec("cd {$workdir} && virtualenv venv");

        if ($this->failed())
            throw new RuntimeException('virtualenv" command has failed.');

        if ($installDependencies) {
            $this->enablePty();

            $this->execPty("cd {$workdir} && source venv/bin/activate");
            $this->read();

            $this->execPty("pip install -r requirements.txt");
            $this->read();

            if ($this->failed())
                throw new RuntimeException('"pip install" command has failed.');
        }
    }
}
