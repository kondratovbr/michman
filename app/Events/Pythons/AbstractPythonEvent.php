<?php declare(strict_types=1);

namespace App\Events\Pythons;

use App\Events\Servers\AbstractServerEvent;
use App\Models\Python;

abstract class AbstractPythonEvent extends AbstractServerEvent
{
    public int $pythonKey;

    public function __construct(Python $python)
    {
        parent::__construct($python->server);

        $this->pythonKey = $python->getKey();
    }
}
