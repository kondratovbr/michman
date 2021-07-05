<?php declare(strict_types=1);

namespace App\Events\DatabaseUsers;

use App\Events\AbstractServerEvent;
use App\Models\DatabaseUser;

abstract class AbstractDatabaseUserEvent extends AbstractServerEvent
{
    public DatabaseUser $databaseUser;

    public function __construct(DatabaseUser $databaseUser)
    {
        parent::__construct($databaseUser->server);

        $this->databaseUser = $databaseUser->withoutRelations();
    }
}
