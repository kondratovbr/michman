<?php declare(strict_types=1);

namespace App\Jobs\Traits;

use App\Collections\EloquentCollection as Collection;
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

        $scriptClass = config("servers.databases.{$server->installedDatabase}.scripts_namespace")
            . '\\' . $scriptName;

        if (! class_exists($scriptClass))
            throw new RuntimeException('No database creation script exists for this database.');

        return App::make($scriptClass);
    }

    /** Check that databases and database users aren't empty and all belong to the same server. */
    protected function validateDatabasesAndUsers(Collection $databaseUsers, Collection $databases): void
    {
        if ($databaseUsers->isEmpty())
            throw new RuntimeException('No database users found.');

        if ($databases->isEmpty())
            throw new RuntimeException('No databases found.');

        if ($databaseUsers->pluck('server_id')->unique()->count() > 1)
            throw new RuntimeException('The database users belong to different servers.');

        if ($databases->pluck('server_id')->unique()->count() > 1)
            throw new RuntimeException('The databases belong to different servers.');

        if (! $databaseUsers->first()->server->is($databases->first()->server))
            throw new RuntimeException('The databases and database users belong to different servers.');
    }
}
