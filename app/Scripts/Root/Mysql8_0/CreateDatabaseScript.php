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

        // CREATE DATABASE IF NOT EXISTS app_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        $this->exec("mysql -u root -p{$server->databaseRootPassword} -e \"CREATE DATABASE IF NOT EXISTS {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci\"");

        if ($this->getExitStatus() !== 0)
            throw new ServerScriptException('Command to create a new database failed.');

        // SHOW DATABASES
        $output = $this->exec("mysql -u root -p{$server->databaseRootPassword} -e \"SHOW DATABASES\"");

        if (! Str::contains($output, $dbName))
            throw new ServerScriptException('New database was not created.');
    }
}
