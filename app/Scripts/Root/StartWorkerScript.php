<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Models\Worker;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class StartWorkerScript extends AbstractServerScript
{
    public function execute(Server $server, Worker $worker, SFTP $rootSsh = null): bool
    {
        $this->init($server, $rootSsh);

        $this->exec("mkdir -p /etc/supervisor/conf.d && mkdir -p /var/log/celery");

        if ($this->sendString($worker->configPath(), $worker->supervisorConfig()) === false)
            throw new ServerScriptException('Failed to send config file to this path: ' . $worker->configPath());

        $this->exec("supervisorctl reread");

        $this->exec("supervisorctl update {$worker->name}");

        // Wait for Celery to start or fail. Its Supervisor config is set to 10s start time.
        $this->setTimeout(30);
        $this->exec("sleep 10");

        $output = $this->exec("supervisorctl status {$worker->name}");
        if ($this->failed() || ! Str::contains($output, 'RUNNING'))
            throw new ServerScriptException('Worker has failed to start.');

        return true;
    }
}
