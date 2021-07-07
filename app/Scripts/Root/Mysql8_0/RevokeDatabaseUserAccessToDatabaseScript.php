<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithMysql;
use App\Support\Str;
use phpseclib3\Net\SFTP;
use RuntimeException;

class RevokeDatabaseUserAccessToDatabaseScript extends AbstractServerScript
{
    use InteractsWithMysql;

    public function execute(
        Server $server,
        string $dbName,
        string $userName,
        SFTP $ssh = null,
    ): void {
        $this->init($server, $ssh);

        $this->execMysql(
            "REVOKE ALL PRIVILEGES ON {$dbName}.* FROM '{$userName}'@'%'",
            'root',
            $server->databaseRootPassword,
        );

        if ($this->getExitStatus() !== 0)
            throw new ServerScriptException('Command to revoke all privileges from a database user failed.');

        $this->execMysql(
            'FLUSH PRIVILEGES',
            'root',
            $server->databaseRootPassword,
        );

        if (Str::contains(
            $this->execMysql(
                "SHOW GRANTS FOR '{$userName}'@'%'",
                'root',
                $server->databaseRootPassword,
            ),
            // Notice that syntax here is a bit different than in the query above -
            // MySQL outputs with backticks.
            "GRANT ALL PRIVILEGES ON `{$dbName}`.* TO `{$userName}`@`%`"
        )) {
            throw new RuntimeException('Privileges were not revoked.');
        }
    }
}
