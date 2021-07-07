<?php declare(strict_types=1);

namespace App\Actions\DatabaseUsers;

use App\Jobs\Servers\DeleteDatabaseUserJob;
use App\Models\DatabaseUser;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class DeleteDatabaseUserAction
{
    public function __construct(
        protected RevokeDatabaseUsersAccessToDatabasesAction $revokeAction,
    ) {}

    public function execute(DatabaseUser $databaseUser): void
    {
        DB::transaction(function () use ($databaseUser) {
            /** @var DatabaseUser $databaseUser */
            $databaseUser = DatabaseUser::query()
                ->lockForUpdate()
                ->findOrFail($databaseUser->getKey());

            $databaseUser->status = DatabaseUser::STATUS_DELETING;
            $databaseUser->save();

            if ($databaseUser->databases->isNotEmpty()) {
                $revokeJob = $this->revokeAction->execute(
                    collect([$databaseUser]),
                    $databaseUser->databases,
                );
            }

            $jobs = [];

            if (! is_null($revokeJob ?? null))
                $jobs[] = $revokeJob;

            $jobs[] = new DeleteDatabaseUserJob($databaseUser);

            Bus::chain($jobs)->dispatch();
        }, 5);
    }
}
