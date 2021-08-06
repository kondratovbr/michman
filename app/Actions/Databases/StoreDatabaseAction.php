<?php declare(strict_types=1);

namespace App\Actions\Databases;

use App\Actions\DatabaseUsers\GrantDatabaseUsersAccessToDatabasesAction;
use App\DataTransferObjects\DatabaseData;
use App\Jobs\AbstractJob;
use App\Jobs\Databases\CreateDatabaseOnServerJob;
use App\Models\Database;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class StoreDatabaseAction
{
    public function __construct(
        protected GrantDatabaseUsersAccessToDatabasesAction $grantAction,
    ) {}

    public function execute(DatabaseData $data, Server $server, Collection $grantedUsers = null, bool $sync = false): Database
    {
        DB::beginTransaction();

        /** @var Database $database */
        $database = $server->databases()->create($data->toArray());

        if (! is_null($grantedUsers) && $grantedUsers->isNotEmpty()) {
            $grantJob = $this->grantAction->execute(
                $grantedUsers,
                collect([$database]),
                $sync,
            );
        }

        if ($sync) {
            CreateDatabaseOnServerJob::dispatchSync($database, true);
            DB::commit();
            $database->refresh();
            return $database;
        }

        DB::commit();

        $jobs = [
            new CreateDatabaseOnServerJob($database),
        ];

        if (! is_null($grantJob ?? null))
            $jobs[] = $grantJob;

        Bus::chain($jobs)->dispatch();

        return $database;
    }
}
