<?php declare(strict_types=1);

namespace App\Actions\DatabaseUsers;

use App\DataTransferObjects\DatabaseUserDto;
use App\Jobs\DatabaseUsers\CreateDatabaseUserOnServerJob;
use App\Models\DatabaseUser;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class StoreDatabaseUserAction
{
    public function __construct(
        protected GrantDatabaseUsersAccessToDatabasesAction $grantAction,
    ) {}

    public function execute(DatabaseUserDto $data, Server $server, Collection $grantedDatabases = null): DatabaseUser
    {
        DB::beginTransaction();

        /** @var DatabaseUser $databaseUser */
        $databaseUser = $server->databaseUsers()->create($data->toArray());

        if (! is_null($grantedDatabases) && $grantedDatabases->isNotEmpty()) {
            $grantJob = $this->grantAction->execute(
                collect([$databaseUser]),
                $grantedDatabases,
            );
        }

        DB::commit();

        $jobs = [
            new CreateDatabaseUserOnServerJob($databaseUser),
        ];

        if (! is_null($grantJob ?? null))
            $jobs[] = $grantJob;

        Bus::chain($jobs)->dispatch();

        return $databaseUser;
    }
}
