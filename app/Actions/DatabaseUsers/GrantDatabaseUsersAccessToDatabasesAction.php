<?php declare(strict_types=1);

namespace App\Actions\DatabaseUsers;

use App\Collections\EloquentCollection;
use App\Jobs\DatabaseUsers\GrantDatabaseUsersAccessToDatabasesJob;
use App\Models\Database;
use App\Models\DatabaseUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GrantDatabaseUsersAccessToDatabasesAction
{
    public function execute(
        BaseCollection $databaseUsers,
        BaseCollection $databases,
        bool $sync = false,
    ): GrantDatabaseUsersAccessToDatabasesJob|null {
        return DB::transaction(function () use ($databaseUsers, $databases, $sync) {
            if ($databaseUsers->isEmpty()) {
                Log::warning(static::class . ' called but no databaseUsers supplied.');
                return null;
            }

            if ($databases->isEmpty()) {
                Log::warning(static::class . ' called but no databases supplied.');
                return null;
            }

            $databaseUsers = $this->lockDatabaseUsers($databaseUsers);
            $databases = $this->lockDatabases($databases);

            $this->runServerChecks($databaseUsers, $databases);

            $this->attachModels($databaseUsers, $databases);

            if ($sync) {
                GrantDatabaseUsersAccessToDatabasesJob::dispatchSync($databaseUsers, $databases, true);
                return null;
            }

            return new GrantDatabaseUsersAccessToDatabasesJob($databaseUsers, $databases);
        }, 5);
    }

    /** Reload and lockForUpdate database users. */
    private function lockDatabaseUsers(BaseCollection $databaseUsers): EloquentCollection
    {
        /** @var EloquentCollection $collection */
        $collection = DatabaseUser::query()
            ->whereIn(
                DatabaseUser::keyName(),
                $databaseUsers->pluck(DatabaseUser::keyName())
            )
            ->lockForUpdate()
            ->get();

        return $collection;
    }

    /** Reload and lockForUpdate databases. */
    private function lockDatabases(BaseCollection $databases): EloquentCollection
    {
        /** @var EloquentCollection $collection */
        $collection = Database::query()
            ->whereIn(
                Database::keyName(),
                $databases->pluck(Database::keyName())
            )
            ->lockForUpdate()
            ->get();

        return $collection;
    }

    /** Check that databases and database users all belong to the same server. */
    private function runServerChecks(Collection $databaseUsers, Collection $databases): void
    {
        if ($databaseUsers->pluck('server_id')->unique()->count() > 1)
            throw new RuntimeException('The database users belong to different servers.');

        if ($databases->pluck('server_id')->unique()->count() > 1)
            throw new RuntimeException('The databases belong to different servers.');

        if (! $databaseUsers->first()->server->is($databases->first()->server))
            throw new RuntimeException('The databases and database users belong to different servers.');
    }

    /** Attach every database user to every database. */
    private function attachModels(Collection $databaseUsers, Collection $databases): void
    {
        /** @var Database $database */
        foreach ($databases as $database) {
            $database->databaseUsers()->attach($databaseUsers);
        }
    }
}
