<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class RegionDto extends AbstractDto
{
    public function __construct(
        public string $name,
        public string $slug,
        /** @var string[] */
        public array $sizes,
        public bool $available,
    ) {}
}
