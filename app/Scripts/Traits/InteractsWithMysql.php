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
                is_null($password) ? null : '[SCRUBBED]',
            ),
        );
    }

    /**
     * Get the list of databases that exist on the server.
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

    /**
     * Get the list of database users that exist on the server.
     */
    protected function getDatabaseUsersMysql(string $dbUser = 'root', string $password = null): array
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
