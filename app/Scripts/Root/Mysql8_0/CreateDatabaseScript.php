<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithMysql;
use App\Support\Arr;
use phpseclib3\Net\SFTP;

class CreateDatabaseScript extends AbstractServerScript
{
    use InteractsWithMysql;

    public function execute(Server $server, string $dbName, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->execMysql(
            "CREATE DATABASE {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci",
            'root',
            $server->databaseRootPassword,
        );

        if ($this->getExitStatus() !== 0)
            throw new ServerScriptException('Command to create a new database failed.');

        if (! Arr::hasValue(
            $this->mysqlGetDatabases('root', $server->databaseRootPassword),
            $dbName
        )) {
            throw new ServerScriptException('New database was not created.');
        }
    }
}
