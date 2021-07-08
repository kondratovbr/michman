<?php declare(strict_types=1);

namespace App\Services;

use App\Jobs\Interfaces\PerformsActionsBeforeDispatching;
use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\Container\Container;
use RuntimeException;

/**
 * Custom queue dispatcher JobDispatcher
 *
 * Allows to perform some tasks synchronously before initially
 * dispatching a job to queue.
 */
class JobDispatcher extends Dispatcher
{
    public function __construct(Container $app, Dispatcher $dispatcher)
    {
        parent::__construct($app, $dispatcher->queueResolver);
    }

    public function dispatchToQueue($command)
    {
        if ($command instanceof PerformsActionsBeforeDispatching)
            $this->beforeDispatching($command);

        return parent::dispatchToQueue($command);
    }

    /**
     * Perform the job's "beforeDispatching" actions.
     */
    protected function beforeDispatching(PerformsActionsBeforeDispatching $job): void
    {
        if (! method_exists($job, 'beforeDispatching'))
            throw new RuntimeException('A job implementing PerformsActionsBeforeDispatching interface must have a "beforeDispatching" public method available. Job class: ' . $job::class);

        $this->container->call([$job, 'beforeDispatching']);
    }
}
