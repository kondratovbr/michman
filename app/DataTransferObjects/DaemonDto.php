<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class DaemonDto extends AbstractDto
{
    public function __construct(
        public string $command,
        public string $username,
        public string|null $directory,
        public int $processes,
        public int $start_seconds,
    ) {}
}
