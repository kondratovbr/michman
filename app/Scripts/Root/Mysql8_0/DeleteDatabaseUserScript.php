<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithMysql;
use App\Support\Arr;
use phpseclib3\Net\SFTP;

class DeleteDatabaseUserScript extends AbstractServerScript
{
    use InteractsWithMysql;

    public function execute(Server $server, string $userName, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->execMysql(
            "DROP USER '{$userName}'@'%'",
            'root',
            $server->databaseRootPassword,
        );

        if ($this->getExitStatus() !== 0)
            throw new ServerScriptException('Command to drop a database user failed.');

        $createdUser = Arr::first(
            $this->getDatabaseUsersMysql('root', $server->databaseRootPassword),
            fn(array $userData) => $userData['user'] === $userName
        );

        if (! is_null($createdUser))
            throw new ServerScriptException('The database user was not deleted.');
    }
}
