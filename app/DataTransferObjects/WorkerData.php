<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class WorkerData extends DataTransferObject
{
    public string $type;
    public string|null $app;
    public int|null $processes;
    /** @var string[]|null  */
    public array|null $queues;
    public int $stop_seconds;
    public int|null $max_tasks_per_child;
    public int|null $max_memory_per_child;
}
