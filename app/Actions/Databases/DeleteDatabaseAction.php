<?php declare(strict_types=1);

namespace App\Actions\Databases;

use App\Actions\DatabaseUsers\RevokeDatabaseUsersAccessToDatabasesAction;
use App\Jobs\Databases\DeleteDatabaseJob;
use App\Models\Database;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class DeleteDatabaseAction
{
    public function __construct(
        protected RevokeDatabaseUsersAccessToDatabasesAction $revokeAction,
    ) {}

    public function execute(Database $database): void
    {
        DB::transaction(function () use ($database) {
            /** @var Database $database */
            $database = $database->freshLockForUpdate('server');

            if ($database->databaseUsers->isNotEmpty()) {
                $revokeJob = $this->revokeAction->execute(
                    $database->databaseUsers,
                    collect([$database]),
                );
            }

            $jobs = [];

            if (! is_null($revokeJob ?? null))
                $jobs[] = $revokeJob;

            $jobs[] = new DeleteDatabaseJob($database);

            Bus::chain($jobs)->dispatch();
        }, 5);
    }
}
