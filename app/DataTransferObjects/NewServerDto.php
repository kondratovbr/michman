<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class NewServerDto extends AbstractDto
{
    public function __construct(
        public string $name,
        public string $region,
        public string $size,
        public string $type,
        public string|null $python_version = null,
        public string $database = 'none',
        public string $cache = 'none',
    ) {}
}
