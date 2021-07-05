<?php declare(strict_types=1);

namespace App\Actions\Databases;

use App\DataTransferObjects\DatabaseData;
use App\Jobs\Servers\CreateDatabaseOnServerJob;
use App\Models\Database;
use App\Models\Server;

// TODO: CRITICAL! Cover with tests.

class StoreDatabaseAction
{
    public function execute(DatabaseData $data, Server $server): Database
    {
        $attributes = $data->toArray();

        $attributes['status'] ??= Database::STATUS_CREATING;

        /** @var Database $database */
        $database = $server->databases()->create($attributes);

        CreateDatabaseOnServerJob::dispatch($database);

        return $database;
    }
}
