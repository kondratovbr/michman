<?php declare(strict_types=1);

namespace App\Jobs\Traits;

use App\Models\Server;
use Illuminate\Support\Facades\App;
use RuntimeException;

trait HandlesDatabases
{
    /**
     * Find a server script class corresponding with the database installed on a server
     * and return an instance of that class created using DI.
     */
    protected function getDatabaseScript(Server $server, string $scriptName): object
    {
        if (empty($server->installedDatabase))
            throw new RuntimeException('No database installed on this server.');

        $scriptClass = (string) config("servers.databases.{$server->installedDatabase}.scripts_namespace")
            . '\\' . $scriptName;

        if (! class_exists($scriptClass))
            throw new RuntimeException('No database creation script exists for this database.');

        return App::make($scriptClass);
    }
}
