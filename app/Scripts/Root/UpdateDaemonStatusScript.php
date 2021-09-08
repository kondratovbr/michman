<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Daemon;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class UpdateDaemonStatusScript extends AbstractServerScript
{
    /**
     * @return string Current daemon's status.
     */
    public function execute(Server $server, Daemon $daemon, SFTP $rootSsh = null): string
    {
        $this->init($server, $rootSsh);

        $output = $this->exec("supervisorctl status {$daemon->name}");
        if ($this->failed())
            throw new ServerScriptException('supervisorctl status command has failed.');

        // See graph: http://supervisord.org/subprocess.html
        return match (true) {
            Str::contains($output, ['STARTING', 'BACKOFF']) => Daemon::STATUS_STARTING,
            Str::contains($output, 'RUNNING') => Daemon::STATUS_ACTIVE,
            default => Daemon::STATUS_FAILED,
        };
    }
}
