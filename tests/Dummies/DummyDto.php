<?php declare(strict_types=1);

namespace Tests\Dummies;

use App\DataTransferObjects\AbstractDto;

class DummyDto extends AbstractDto
{
    public function __construct(
        public string $foo,
        public string $bar,
        public int $n,
        public array $arr,
    ) {}
}
