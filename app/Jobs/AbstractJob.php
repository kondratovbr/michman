<?php declare(strict_types=1);

namespace App\Jobs;

use App\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

abstract class AbstractJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected bool $sync = false;

    public function __construct()
    {
        $this->setQueue($this->getQueue());
    }

    /** Get the name of the queue for this job. */
    abstract protected function getQueue(): string;

    /**
     * Mark this job instance as being executed synchronously.
     *
     * This is used to execute the job synchronously inside
     * another job when they shouldn't normally overlap.
     *
     * @return $this
     */
    public function sync(bool|null $sync = true): static
    {
        $this->sync = $sync ?? false;
        return $this;
    }

    /** Set a queue for this job. */
    protected function setQueue(string $queueName): void
    {
        if (! Arr::hasValue(config('queue.queues'), $queueName))
            throw new RuntimeException("Queue {$queueName} is not configured.");

        $this->onQueue($queueName);
    }
}
