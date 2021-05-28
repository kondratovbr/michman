<?php declare(strict_types=1);

namespace App\Jobs;

use App\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class AbstractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Set a queue for this job.
     */
    protected function setQueue(string $queueName): void
    {
        if (! Arr::hasValue(config('queue.queues'), $queueName))
            throw new RuntimeException("Queue {$queueName} is not configured.");

        $this->onQueue($queueName);
    }
}
