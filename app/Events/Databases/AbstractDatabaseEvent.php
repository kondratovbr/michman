<?php declare(strict_types=1);

namespace App\Events\Databases;

use App\Events\AbstractServerEvent;
use App\Models\Database;

abstract class AbstractDatabaseEvent extends AbstractServerEvent
{
    public Database $database;

    public function __construct(Database $database)
    {
        parent::__construct($database->server);

        $this->database = $database->withoutRelations();
    }
}
