<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FoobarEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $queue = 'events';

    public function __construct()
    {
        //
    }

    public function broadcastOn(): Channel
    {
        return new Channel('foo');
    }

    public function broadcastAs(): string
    {
        return 'bar';
    }
}
