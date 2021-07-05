<?php declare(strict_types=1);

namespace App\Actions\DatabaseUsers;

use App\DataTransferObjects\DatabaseUserData;
use App\Jobs\Servers\CreateDatabaseUserOnServerJob;
use App\Models\DatabaseUser;
use App\Models\Server;

// TODO: CRITICAL! Cover with tests!

class StoreDatabaseUserAction
{
    public function execute(DatabaseUserData $data, Server $server): DatabaseUser
    {
        $attributes = $data->toArray();

        $attributes['status'] = DatabaseUser::STATUS_CREATING;

        /** @var DatabaseUser $databaseUser */
        $databaseUser = $server->databaseUsers()->create($attributes);

        CreateDatabaseUserOnServerJob::dispatch($databaseUser);

        return $databaseUser;
    }
}
