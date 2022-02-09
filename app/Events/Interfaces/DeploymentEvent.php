<?php declare(strict_types=1);

namespace App\Events\Interfaces;

use App\Models\Deployment;

interface DeploymentEvent
{
    public function deployment(): Deployment|null;
}
