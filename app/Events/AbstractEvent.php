<?php declare(strict_types=1);

namespace App\Events;

abstract class AbstractEvent
{
    /**
     * The name of the queue on which to place the broadcasting job.
     *
     * @var string
     */
    public $queue = 'broadcasting';
}
