<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Daemon;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class StartDaemonScript extends AbstractServerScript
{
    public function execute(Server $server, Daemon $daemon, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $this->exec("mkdir -p /etc/supervisor/conf.d && mkdir -p /var/log/michman");
        if ($this->failed())
            throw new ServerScriptException('Failed to create config and log directories.');

        if ($this->sendString($daemon->configPath(), $daemon->supervisorConfig()) === false)
            throw new ServerScriptException('Failed to send config file to this path: ' . $daemon->configPath());

        $this->exec("supervisorctl reread");
        if ($this->failed())
            throw new ServerScriptException('supervisorctl reread command has failed.');

        $this->exec("supervisorctl update {$daemon->name}");
        if ($this->failed())
            throw new ServerScriptException('supervisorctl update command has failed.');

        /*
         * We aren't going to wait for the daemon to start,
         * because the users may have set its startup time to
         * some high number. We'll update its status later instead.
         */
    }
}
