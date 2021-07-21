<?php declare(strict_types=1);

namespace App\Actions\DatabaseUsers;

use App\Collections\EloquentCollection;
use App\Jobs\DatabaseUsers\UpdateDatabaseUserPasswordJob;
use App\Jobs\DatabaseUsers\GrantDatabaseUsersAccessToDatabasesJob;
use App\Jobs\DatabaseUsers\RevokeDatabaseUsersAccessToDatabasesJob;
use App\Models\DatabaseUser;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class UpdateDatabaseUserAction
{
    public function execute(
        DatabaseUser $databaseUser,
        string $newPassword,
        EloquentCollection $grantedDatabases,
    ): DatabaseUser {
        return DB::transaction(function () use ($databaseUser, $newPassword, $grantedDatabases) {
            /** @var DatabaseUser $databaseUser */
            $databaseUser = DatabaseUser::query()->lockForUpdate()->findOrFail($databaseUser->getKey());

            $jobs = [];

            /** @var EloquentCollection $databasesToRevoke */
            $databasesToRevoke = $databaseUser->databases->diff($grantedDatabases);
            $databasesToGrant = $grantedDatabases->diff($databaseUser->databases);

            if (! empty($newPassword)) {
                $databaseUser->password = $newPassword;
                $databaseUser->save();
                $jobs[] = new UpdateDatabaseUserPasswordJob($databaseUser);
            }

            $databaseUser->databases()->sync($grantedDatabases);

            if ($databasesToRevoke->isNotEmpty()) {
                $jobs[] = new RevokeDatabaseUsersAccessToDatabasesJob(
                    collection([$databaseUser]),
                    $databasesToRevoke,
                );
            }

            if ($databasesToGrant->isNotEmpty()) {
                $jobs[] = new GrantDatabaseUsersAccessToDatabasesJob(
                    collection([$databaseUser]),
                    $databasesToGrant,
                );
            }

            Bus::chain($jobs)->dispatch();

            return $databaseUser;
        }, 5);
    }
}
