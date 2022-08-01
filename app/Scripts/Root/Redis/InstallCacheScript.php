<?php declare(strict_types=1);

namespace App\Scripts\Root\Redis;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithApt;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class InstallCacheScript extends AbstractServerScript
{
    use InteractsWithApt;

    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->aptUpdate();
        $this->aptInstall('redis-server');

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
