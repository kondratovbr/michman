<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Models\Worker;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

/*
 * TODO: CRITICAL! A refactoring idea - add the "throw new ServerScriptException" into the parent class and have an option on "exec" commands to skip it when necessary.
 */

class StartWorkerScript extends AbstractServerScript
{
    public function execute(Server $server, Worker $worker, SFTP $rootSsh = null): bool
    {
        $this->init($server, $rootSsh);

        $this->exec("mkdir -p /etc/supervisor/conf.d");
        if ($this->failed())
            throw new ServerScriptException('Failed: mkdir -p /etc/supervisor/conf.d');

        if ($this->sendString($worker->configPath(), $worker->supervisorConfig()) === false)
            throw new ServerScriptException('Failed to send config file to this path: ' . $worker->configPath());

        $this->exec("supervisorctl reread");
        if ($this->failed())
            throw new ServerScriptException('supervisorctl reread command has failed.');

        // TODO: CRITICAL! Verify that the worker is started somehow and update the worker status if it has failed.

        return true;
    }
}
