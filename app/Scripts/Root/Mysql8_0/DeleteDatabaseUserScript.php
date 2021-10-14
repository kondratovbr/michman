<?php declare(strict_types=1);

namespace App\Scripts\Root\Mysql8_0;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithMysql;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SFTP;

class DeleteDatabaseUserScript extends AbstractServerScript
{
    use InteractsWithMysql;

    public function execute(Server $server, string $userName, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        if (! $this->mysqlUserExists($userName, 'root', $server->databaseRootPassword)) {
            Log::warning("The MySQL user {$userName} that was requested to be deleted doesn't exist.");
            return;
        }

        $this->execMysql(
            "DROP USER '{$userName}'@'%'",
            'root',
            $server->databaseRootPassword,
        );

        if ($this->mysqlUserExists($userName, 'root', $server->databaseRootPassword))
            throw new ServerScriptException('The database user was not deleted.');
    }
}
