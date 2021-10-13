<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Models\Worker;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\States\Workers\Active;
use App\States\Workers\Failed;
use App\States\Workers\Starting;
use App\States\Workers\WorkerState;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class UpdateWorkerStateScript extends AbstractServerScript
{
    public function execute(Server $server, Worker $worker, SFTP $ssh = null): WorkerState
    {
        $this->init($server, $ssh);

        $output = $this->exec("supervisorctl status {$worker->name}");
        if ($this->failed())
            throw new ServerScriptException('supervisorctl status command has failed.');

        // See graph: http://supervisord.org/subprocess.html
        return match (true) {
            Str::contains($output, ['STARTING', 'BACKOFF']) => new Starting($worker),
            Str::contains($output, 'RUNNING') => new Active($worker),
            default => new Failed($worker),
        };
    }
}
