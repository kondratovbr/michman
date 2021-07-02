<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class CreateDatabaseScript extends AbstractServerScript
{
    public function execute(Server $server, string $dbName, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->execMysql(
            "CREATE DATABASE IF NOT EXISTS {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci",
            'root',
            $server->databaseRootPassword,
        );

        if ($this->getExitStatus() !== 0)
            throw new ServerScriptException('Command to create a new database failed.');

        $output = $this->execMysql(
            "SHOW DATABASES",
            'root',
            $server->databaseRootPassword,
        );

        if (! Str::contains($output, $dbName))
            throw new ServerScriptException('New database was not created.');
    }
}
