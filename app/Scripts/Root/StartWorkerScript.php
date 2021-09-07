<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Models\Worker;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;
use phpseclib3\Net\SFTP;

/*
 * TODO: CRITICAL! A refactoring idea - add the "throw new ServerScriptException" into the parent class and have an option on "exec" commands to skip it when necessary.
 */

class StartWorkerScript extends AbstractServerScript
{
    public function execute(Server $server, Worker $worker, SFTP $rootSsh = null): bool
    {
        $this->init($server, $rootSsh);

        $this->exec("mkdir -p /etc/supervisor/conf.d && mkdir -p /var/log/celery");
        if ($this->failed())
            throw new ServerScriptException('Failed to create config and log directories.');

        if ($this->sendString($worker->configPath(), $worker->supervisorConfig()) === false)
            throw new ServerScriptException('Failed to send config file to this path: ' . $worker->configPath());

        $this->exec("supervisorctl reread");
        if ($this->failed())
            throw new ServerScriptException('supervisorctl reread command has failed.');

        $this->exec("supervisorctl update {$worker->name}");
        if ($this->failed())
            throw new ServerScriptException('supervisorctl update command has failed.');

        // Wait for Celery to start or fail. Config is set to 10s start time.
        $this->setTimeout(30);
        $this->exec("sleep 10");

        $output = $this->exec("supervisorctl status {$worker->name}");
        if ($this->failed() || ! Str::contains('RUNNING', $output))
            throw new ServerScriptException('Worker has failed to start.');

        return true;
    }
}
