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
    /** Execute a MySQL query locally on the server over SSH. */
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
                is_null($password) ? null : '[SCRUBBED]',
            ),
        );
    }

    /**
     * Get the list of databases that exist on the server.
     *
     * @return string[]
     */
    protected function mysqlGetDatabases(string $dbUser = 'root', string $password = null): array
    {
        $output = $this->execMysql(
            'SHOW DATABASES',
            $dbUser,
            $password
        );

        // TODO: Can I achieve this cleaner? There's gotta be an easier to work with mode for MySQL CLI.

        // Clear the output from preceding garbage.
        $output = explode('Database', $output, 2)[1];

        // Split the output values by lines.
        $lines = Str::splitLines($output);

        // There may be empty lines so filter them.
        $lines = Arr::filter($lines, fn(string $line) => ! empty($line));

        // Also trim them just in case.
        return Arr::trimValues($lines);
    }

    /** Get the list of database users that exist on the server. */
    protected function mysqlGetDatabaseUsers(string $dbUser = 'root', string $password = null): array
    {
        $output = $this->execMysql(
            'SELECT host, user FROM mysql.user',
            $dbUser,
            $password
        );

        // TODO: Can I achieve this cleaner? There's gotta be an easier to work with mode for MySQL CLI.

        // Clear the output from preceding garbage.
        $output = explode('user', $output, 2)[1];

        // Split the output values by lines.
        $lines = Str::splitLines($output);

        // There will be empty lines, so filter them.
        $lines = Arr::filter($lines, fn(string $line) => ! empty($line));

        // Split the lines by values.
        return Arr::map($lines, function (string $line): array {
            // MySQL CLI separates values on a line by \t (next tab stop) character.
            $values = explode("\t", $line);
            // Also trim them just in case.
            $values = Arr::trimValues($values);

            return [
                'host' => $values[0],
                'user' => $values[1],
            ];
        });
    }

    /** Check if a database user has been granted full access to a database on the server. */
    protected function mysqlUserHasGrants(
        string $dbName,
        string $userName,
        string $dbUser = 'root',
        string $password = null,
    ): bool {
        return Str::contains(
            $this->execMysql(
                "SHOW GRANTS FOR '{$userName}'@'%'",
                $dbUser,
                $password,
            ),
            // Notice that syntax here is a bit different than in the query above -
            // MySQL outputs with backticks.
            "GRANT ALL PRIVILEGES ON `{$dbName}`.* TO `{$userName}`@`%`"
        );
    }

    /**
     * Get a database user information as:
     * ['host' => '%', 'user' => 'theuser']
     * or null if the user doesn't exist.
     */
    protected function mysqlGetDatabaseUser(string $userName, string $dbUser = 'root', string $password = null): array|null
    {
        return Arr::first(
            $this->mysqlGetDatabaseUsers($dbUser, $password),
            fn(array $userData) => $userData['user'] === $userName
        );
    }

    /** Check if a database user exists on the server. */
    protected function mysqlUserExists(string $userName, string $dbUser = 'root', string $password = null): bool
    {
        return ! is_null($this->mysqlGetDatabaseUser($userName, $dbUser, $password));
    }

    /** Create a command to run an SQL query over a local MySQL server on a remote server over SSH. */
    private function mysqlCommand(string $query, string $dbUser, string $password = null): string
    {
        return isset($password)
            ? "mysql -u {$dbUser} -p{$password} -e \"{$query}\""
            : "mysql -u {$dbUser} -e \"{$query}\"";
    }
}
