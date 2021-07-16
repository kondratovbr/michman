<?php declare(strict_types=1);

namespace App\Actions\DatabaseUsers;

use App\Models\DatabaseUser;
use Illuminate\Database\Eloquent\Collection;

class UpdateDatabaseUserAction
{
    public function execute(DatabaseUser $databaseUser, string $newPassword, Collection $grantedDatabases): DatabaseUser
    {
        // TODO: CRITICAL! Implement and test!

        return $databaseUser;

        dd(static::class, $databaseUser, $newPassword, $grantedDatabases);

        //
    }
}
