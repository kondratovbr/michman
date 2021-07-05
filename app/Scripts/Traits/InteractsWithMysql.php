<?php declare(strict_types=1);

namespace App\Scripts\Traits;

use App\Scripts\AbstractServerScript;
use App\Support\Arr;
use App\Support\Str;

/**
 * Trait InteractsWithMysql for server scripts.
 *
 * @mixin AbstractServerScript
 */
trait InteractsWithMysql
{
    /**
     * Execute a MySQL query locally on the server over SSH.
     */
    protected function execMysql(string $query, string $dbUser = 'root', string $password = null): string|bool
    {
        $this->initialize();

        return $this->exec(
            $this->mysqlCommand($query, $dbUser, $password),
            true,
            false,
            $this->mysqlCommand(
                $query,
                $dbUser,
                // Scrubbing the password from logs.
                is_null($password) ? null : 'PASSWORD',
            ),
        );
    }

    /**
     * Get a list of databases that exist on the server.
     *
     * @return string[]
     */
    protected function getDatabasesMysql(string $dbUser = 'root', string $password = null): array
    {
        $output = $this->execMysql(
            'SHOW DATABASES',
            $dbUser,
            $password
        );

        /*
         * The "explode" here is to make sure we don't search for the dbName in the text that MySQL
         * outputs before the list of databases. Just in case the dbName matches some of it,
         * like if it was "Database" or "command" for some reason.
         * See what MySQL outputs for the SHOW DATABASES command,
         * notice that it is different depending on the SSH session being interactive or not.
         */
        $databases = Str::splitLines(explode('Database', $output, 2)[1]);

        return Arr::map($databases, fn (string $dbName) => trim($dbName));
    }

    /**
     * Create a command to run an SQL query over a local MySQL server on a remote server over SSH.
     */
    private function mysqlCommand(string $query, string $dbUser, string $password = null): string
    {
        return isset($password)
            ? "mysql -u {$dbUser} -p{$password} -e \"{$query}\""
            : "mysql -u {$dbUser} -e \"{$query}\"";
    }
}
