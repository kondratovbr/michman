<?php declare(strict_types=1);

namespace App\Events\Projects;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/*
 * TODO: CRITICAL! This event gets broadcasted only on ProjectChannel due to the fact that project has to be detached from servers before deletion - we can't get the servers it was attached to. Is this OK?
 */

class ProjectDeletedEvent extends AbstractProjectEvent implements ShouldBroadcast
{
    //
}
