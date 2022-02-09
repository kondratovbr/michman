<?php declare(strict_types=1);

namespace App\Events\Traits;

use App\Support\Arr;
use Illuminate\Broadcasting\Channel;

trait Broadcasted
{
    abstract protected function getChannels(): Channel|array|null;

    public function broadcastOn(): Channel|array
    {
        return Arr::filter(
            Arr::wrap($this->getChannels()),
            fn($item) => ! empty($item)
        );
    }
}
