<?php declare(strict_types=1);

namespace App\Scripts\Root\Redis;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class InstallCacheScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min
        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get update -y');
        $this->read();
        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get install -y redis-server');
        $this->read();
        $this->disablePty();

        $this->sendFile(
            '/etc/redis/redis.conf',
            base_path('servers/redis/redis.conf'),
        );

        $this->exec('systemctl restart redis.service');
        // Wait a bit in case it doesn't start immediately.
        $this->setTimeout(60);
        $this->exec('sleep 30');

        $output = $this->exec('systemctl status redis');
        if (
            ! Str::contains(Str::lower($output), 'active (running)')
            || $this->failed()
        ) {
            throw new ServerScriptException('Redis failed to start.');
        }
    }
}
