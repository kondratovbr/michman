<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class CreateProjectVenvScript extends AbstractServerScript
{
    // TODO: IMPORTANT! Make sure to implement some feedback for the user in case of some problems here. See the notification system.

    public function execute(
        Server $server,
        Project $project,
        SFTP $ssh = null,
    ): void {
        $username = $project->serverUsername;
        $workdir = "/home/$username/$project->domain";

        $this->init($server, $ssh, $project->serverUsername);

        $this->exec("cd $workdir && virtualenv venv");

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min - pip install may take a long time if there's a lot of stuff to install.

        if (! empty($project->requirementsFile)) {

            $this->execPty("cd $workdir && source venv/bin/activate && pip --quiet install -r $project->requirementsFile --ignore-installed && deactivate");
            $this->read();

            if ($this->failed())
                throw new ServerScriptException("\"pip install -r $project->requirementsFile\" command has failed.");
        }

        $this->execPty("cd $workdir && source venv/bin/activate && pip --quiet install gunicorn && deactivate");
        $this->read();

        if ($this->failed())
            throw new ServerScriptException('"pip install gunicorn" command has failed.');

        $this->disablePty();
    }
}
