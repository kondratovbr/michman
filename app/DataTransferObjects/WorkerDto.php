<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class WorkerDto extends AbstractDto
{
    public function __construct(
        public string $type,
        public int $stop_seconds,
        public string|null $app = null,
        public int|null $processes = null,
        /** @param null|string[] */
        public array|null $queues = null,
        public int|null $max_tasks_per_child = null,
        public int|null $max_memory_per_child = null,
    ) {}
}
