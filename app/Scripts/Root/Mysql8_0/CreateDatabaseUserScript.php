<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithMysql;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SFTP;

class CreateDatabaseUserScript extends AbstractServerScript
{
    use InteractsWithMysql;

    public function execute(
        Server $server,
        string $userName,
        string $password,
        SFTP $ssh = null,
    ): void {
        $this->init($server, $ssh);

        if ($this->mysqlUserExists($userName, 'root', $server->databaseRootPassword)) {
            Log::warning("The MySQL user {$userName} that was requested to be created already exists.");
            return;
        }

        $this->execMysql(
            "CREATE USER '{$userName}'@'%' IDENTIFIED BY '{$password}'",
            'root',
            $server->databaseRootPassword,
        );

        $createdUser = $this->mysqlGetDatabaseUser($userName, 'root', $server->databaseRootPassword);

        if (is_null($createdUser))
            throw new ServerScriptException('New database user was not created.');

        if ($createdUser['host'] !== '%')
            throw new ServerScriptException('New database user was created with a wrong host.');
    }
}
