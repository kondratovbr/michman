<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class InstallDatabaseJob extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        // TODO: IMPORTANT! I should figure out what to do if something here fails.

        $this->init($server, $ssh);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min
        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server-8.0 mysql-client-8.0');
        $this->read();
        $this->disablePty();

        $this->exec('systemctl start mysql');

        // Just in case MySQL was installed and configured before we'll check if we have access with a proper password already.
        if (Str::contains((string) $this->exec("mysql -u root -p{$server->databaseRootPassword} -e \"SHOW DATABASES\"", true), 'information_schema'))
            return;

        // ALTER USER 'root'@'localhost' IDENTIFIED WITH 'caching_sha2_password' BY 'password'; flush privileges;
        $output = $this->exec("mysql -u root -e \"ALTER USER 'root'@'localhost' IDENTIFIED WITH 'caching_sha2_password' BY '{$server->databaseRootPassword}'; flush privileges;\"", true);

        if (Str::contains(Str::lower($output), 'access denied') || $this->getExitStatus() !== 0)
            throw new ServerScriptException('Failed accessing the database as root.');

        // SHOW DATABASES
        $output = $this->exec("mysql -u root -p{$server->databaseRootPassword} -e \"SHOW DATABASES\"", true);

        if (! Str::contains((string) $output, 'information_schema'))
            throw new ServerScriptException('Failed accessing the database as root after setting a password.');
    }
}
