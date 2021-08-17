<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithMysql;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SFTP;

class GrantDatabaseUserAccessToDatabaseScript extends AbstractServerScript
{
    use InteractsWithMysql;

    public function execute(
        Server $server,
        string $dbName,
        string $userName,
        SFTP $ssh = null,
    ): void {
        $this->init($server, $ssh);

        if ($this->mysqlUserHasGrants(
            $dbName,
            $userName,
            'root',
            $server->databaseRootPassword,
        )) {
            Log::warning("The dbUser '{$userName}' already has privileges on database '{$dbName}' that were requested to be granted.");
            return;
        }

        $this->execMysql(
            "GRANT ALL PRIVILEGES ON {$dbName}.* TO '{$userName}'@'%'",
            'root',
            $server->databaseRootPassword,
        );

        if ($this->getExitStatus() !== 0)
            throw new ServerScriptException('Command to grant all privileges to a database user failed.');

        $this->execMysql(
            'FLUSH PRIVILEGES',
            'root',
            $server->databaseRootPassword,
        );

        if (! $this->mysqlUserHasGrants(
            $dbName,
            $userName,
            'root',
            $server->databaseRootPassword,
        )) {
            throw new ServerScriptException('Privileges were not granted.');
        }
    }
}
