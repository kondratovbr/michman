<?php declare(strict_types=1);

namespace App\Actions\Databases;

use App\Jobs\Servers\DeleteDatabaseJob;
use App\Models\Database;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

// TODO: CRITICAL! As per MySQL docs I also have to revoke permissions for database users - they aren't automatically cleaned when a database is deleted.

class DeleteDatabaseAction
{
    public function execute(Database $database): void
    {
        DB::transaction(function () use ($database) {
            /** @var Database $database */
            $database = Database::query()
                ->with('server')
                ->lockForUpdate()
                ->findOrFail($database->getKey());

            $database->status = Database::STATUS_DELETING;
            $database->save();

            DeleteDatabaseJob::dispatch($database);
        }, 5);
    }
}
