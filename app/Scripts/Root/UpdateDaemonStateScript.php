<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Daemon;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\States\Daemons\Active;
use App\States\Daemons\DaemonState;
use App\States\Daemons\Failed;
use App\States\Daemons\Starting;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class UpdateDaemonStateScript extends AbstractServerScript
{
    public function execute(Server $server, Daemon $daemon, SFTP $rootSsh = null): DaemonState
    {
        $this->init($server, $rootSsh);

        $output = $this->exec("supervisorctl status {$daemon->name}");
        if ($this->failed())
            throw new ServerScriptException('supervisorctl status command has failed.');

        // See graph: http://supervisord.org/subprocess.html
        return match (true) {
            Str::contains($output, ['STARTING', 'BACKOFF']) => new Starting($daemon),
            Str::contains($output, 'RUNNING') => new Active($daemon),
            default => new Failed($daemon),
        };
    }
}
