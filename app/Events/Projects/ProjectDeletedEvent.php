<?php declare(strict_types=1);

namespace App\Events\Projects;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProjectDeletedEvent extends AbstractProjectEvent implements ShouldBroadcast
{
    //
}
