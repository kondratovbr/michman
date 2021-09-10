<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class SizeDto extends AbstractDto
{
    public function __construct(
        public string $slug,
        public float $transfer,
        public float $priceMonthly,
        public int $memoryMb,
        public int $cpus,
        public int $diskGb,
        /** @var string[] */
        public array $regions,
        public bool $available,
        public string $description = '',
    ) {}
}
