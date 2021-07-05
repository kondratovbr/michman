<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithMysql;
use App\Support\Arr;
use phpseclib3\Net\SFTP;

// TODO: CRITICAL! Cover with tests?

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

        $this->execMysql(
            "CREATE USER '{$userName}'@'%' IDENTIFIED BY '{$password}'",
            'root',
            $server->databaseRootPassword,
        );

        if ($this->getExitStatus() !== 0)
            throw new ServerScriptException('Command to create a new database user failed.');

        $createdUser = Arr::first(
            $this->getDatabaseUsersMysql('root', $server->databaseRootPassword),
            fn(array $userData) => $userData['user'] === $userName
        );

        if (is_null($createdUser)) {
            throw new ServerScriptException('New user was not created.');
        }

        if ($createdUser['host'] !== '%') {
            throw new ServerScriptException('New user was created with a wrong host.');
        }
    }
}
