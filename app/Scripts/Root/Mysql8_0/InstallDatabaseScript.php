<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithApt;
use App\Scripts\Traits\InteractsWithMysql;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class InstallDatabaseScript extends AbstractServerScript
{
    use InteractsWithMysql;
    use InteractsWithApt;

    /** @var string MySQL authentication plugin to configure for root. */
    protected const MYSQL_AUTH_PLUGIN = 'caching_sha2_password';

    public function execute(Server $server, SFTP $ssh = null): void
    {
        // TODO: IMPORTANT! I should figure out what to do if something here fails.

        $this->init($server, $ssh);

        $this->aptUpdate();

        $this->aptInstall([
            'mysql-server-8.0',
            'mysql-client-8.0',
            'libmysqlclient-dev',
        ]);

        $this->exec('systemctl start mysql');

        // Wait a bit in case MySQL needs some time to start.
        $this->setTimeout(60);
        $this->exec("sleep 30");

        // Verify that MySQL has started.
        $output = $this->exec('systemctl status mysql');
        if (
            ! Str::contains(Str::lower($output), 'active (running)')
            || $this->failed()
        ) {
            throw new ServerScriptException('MySQL failed to start.');
        }

        $authPlugin = static::MYSQL_AUTH_PLUGIN;

        // Just in case MySQL was installed and configured before we'll check if we have access with a proper password already.
        if (Str::contains(
            (string) $this->execMysql(
                'SELECT plugin FROM mysql.user WHERE User = \'root\'',
                'root',
                $server->databaseRootPassword,
            ),
            $authPlugin
        )) {
            return;
        }

        $output = $this->execMysql(
            "ALTER USER 'root'@'localhost' IDENTIFIED WITH '$authPlugin' BY '{$server->databaseRootPassword}'; flush privileges;"
        );

        if (
            Str::contains(Str::lower($output), 'access denied')
            || $this->failed()
        ) {
            throw new ServerScriptException('Failed accessing the database as root.');
        }

        $output = $this->execMysql(
            "SHOW DATABASES",
            'root',
            $server->databaseRootPassword,
        );

        if (! Str::contains((string) $output, 'information_schema'))
            throw new ServerScriptException('Failed accessing the database as root after setting a password.');
    }
}
