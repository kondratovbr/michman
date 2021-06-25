<?php declare(strict_types=1);

namespace App\Events\Pythons;

use App\Events\AbstractServerEvent;
use App\Models\Python;

abstract class AbstractPythonEvent extends AbstractServerEvent
{
    public Python $python;

    public function __construct(Python $python)
    {
        parent::__construct($python->server);

        $this->python = $python->withoutRelations();
    }
}
