<?php declare(strict_types=1);

namespace App\Events\Pythons;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PythonInstalledEvent extends AbstractPythonEvent implements ShouldBroadcast
{
    public function broadcastAs(): string
    {
        return 'pythons.installed';
    }
}
