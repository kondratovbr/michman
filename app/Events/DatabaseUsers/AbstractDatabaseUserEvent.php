<?php declare(strict_types=1);

namespace App\Events\DatabaseUsers;

use App\Events\Servers\AbstractServerEvent;
use App\Models\DatabaseUser;

abstract class AbstractDatabaseUserEvent extends AbstractServerEvent
{
    public int $databaseUserKey;

    public function __construct(DatabaseUser $databaseUser)
    {
        parent::__construct($databaseUser->server);

        $this->databaseUserKey = $databaseUser->getKey();
    }
}
