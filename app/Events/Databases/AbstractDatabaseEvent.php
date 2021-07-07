<?php declare(strict_types=1);

namespace App\Events\Databases;

use App\Events\Servers\AbstractServerEvent;
use App\Models\Database;

abstract class AbstractDatabaseEvent extends AbstractServerEvent
{
    public int $databaseKey;

    public function __construct(Database $database)
    {
        parent::__construct($database->server);

        $this->databaseKey = $database->getKey();
    }
}
