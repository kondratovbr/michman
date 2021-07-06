<?php declare(strict_types=1);

namespace App\Actions\DatabaseUsers;

use App\DataTransferObjects\DatabaseUserData;
use App\Jobs\Servers\CreateDatabaseUserOnServerJob;
use App\Models\DatabaseUser;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class StoreDatabaseUserAction
{
    public function __construct(
        protected GrantDatabaseUsersAccessToDatabasesAction $grantAction,
    ) {}

    public function execute(DatabaseUserData $data, Server $server, Collection $grantedDatabases = null): DatabaseUser
    {
        $attributes = $data->toArray();

        $attributes['status'] = DatabaseUser::STATUS_CREATING;

        DB::beginTransaction();

        /** @var DatabaseUser $databaseUser */
        $databaseUser = $server->databaseUsers()->create($attributes);

        if (! is_null($grantedDatabases) && $grantedDatabases->isNotEmpty()) {
            $grantJob = $this->grantAction->execute(
                collect([$databaseUser]),
                $grantedDatabases,
            );
        }

        DB::commit();

        Bus::chain([
            new CreateDatabaseUserOnServerJob($databaseUser),
            $grantJob ?? null,
        ])->dispatch();

        return $databaseUser;
    }
}
