<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class ProviderDto extends AbstractDto
{
    public function __construct(
        public string $provider,
        public string|null $token,
        public string|null $key,
        public string|null $secret,
        public string|null $name,
    ) {}
}
