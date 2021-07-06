<?php declare(strict_types=1);

namespace App\Actions\Databases;

use App\Actions\DatabaseUsers\GrantDatabaseUsersAccessToDatabasesAction;
use App\DataTransferObjects\DatabaseData;
use App\Jobs\Servers\CreateDatabaseOnServerJob;
use App\Models\Database;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class StoreDatabaseAction
{
    public function __construct(
        protected GrantDatabaseUsersAccessToDatabasesAction $grantAction,
    ) {}

    public function execute(DatabaseData $data, Server $server, Collection $grantedUsers = null): Database
    {
        $attributes = $data->toArray();

        $attributes['status'] ??= Database::STATUS_CREATING;

        DB::beginTransaction();

        /** @var Database $database */
        $database = $server->databases()->create($attributes);

        if (! is_null($grantedUsers) && $grantedUsers->isNotEmpty()) {
            $grantJob = $this->grantAction->execute(
                $grantedUsers,
                collect([$database]),
            );
        }

        DB::commit();

        Bus::chain([
            new CreateDatabaseOnServerJob($database),
            $grantJob ?? null,
        ])->dispatch();

        return $database;
    }
}
