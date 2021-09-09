<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class DatabaseUserDto extends AbstractDto
{
    public function __construct(
        public string $name,
        public string $password,
    ) {}
}
