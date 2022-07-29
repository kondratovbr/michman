<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\InteractsWithMysql;
use phpseclib3\Net\SFTP;

class UpdateDatabaseUserPasswordScript extends AbstractServerScript
{
    use InteractsWithMysql;

    /** @var string MySQL authentication plugin to configure for users. */
    protected const MYSQL_AUTH_PLUGIN = 'caching_sha2_password';

    public function execute(
        Server $server,
        string $userName,
        string $password,
        SFTP $ssh = null,
    ): void {
        $this->init($server, $ssh);

        $authPlugin = static::MYSQL_AUTH_PLUGIN;

        $this->execMysql(
            "ALTER USER '$userName'@'%' IDENTIFIED WITH '$authPlugin' BY '$password'; flush privileges;",
            'root',
            $server->databaseRootPassword,
        );
    }
}
