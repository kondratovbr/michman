<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithMysql;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SFTP;

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

        if (! $this->mysqlUserHasGrants(
            $dbName,
            $userName,
            'root',
            $server->databaseRootPassword,
        )) {
            Log::warning("The dbUser '{$userName}' doesn't have privileges on database '{$dbName}' that were requested to be revoked.");
            return;
        }

        $this->execMysql(
            "REVOKE ALL PRIVILEGES ON {$dbName}.* FROM '{$userName}'@'%'",
            'root',
            $server->databaseRootPassword,
        );

        $this->execMysql(
            'FLUSH PRIVILEGES',
            'root',
            $server->databaseRootPassword,
        );

        if ($this->mysqlUserHasGrants(
            $dbName,
            $userName,
            'root',
            $server->databaseRootPassword,
        )) {
            throw new ServerScriptException('Privileges were not revoked.');
        }
    }
}
