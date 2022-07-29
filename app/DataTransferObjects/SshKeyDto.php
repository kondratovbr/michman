<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class SshKeyDto extends AbstractDto
{
    public function __construct(
        public string $name,
        public string $publicKey,
        public string|null $id = null,
        public string|null $fingerprint = null,
    ) {}
}
