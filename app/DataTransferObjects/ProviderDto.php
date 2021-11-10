<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class ProviderDto extends AbstractDto
{
    public function __construct(
        public string $provider,
        public string|null $name,
        public AuthTokenDto|null $token,
    ) {}
}
