<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class ServerDto extends AbstractDto
{
    public function __construct(
        public string $id,
        public string $name,
        // IP can be null if it wasn't yet attached to the server by the provider.
        public string|null $publicIp4,
    ) {}
}
