<?php declare(strict_types=1);

namespace App\Events\VcsProviders;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VcsProviderUpdatedEvent extends AbstractVcsProviderEvent implements ShouldBroadcast
{
    //
}
