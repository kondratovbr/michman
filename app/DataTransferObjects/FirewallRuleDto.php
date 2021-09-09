<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class FirewallRuleDto extends AbstractDto
{
    public function __construct(
        public string $name,
        public string $port,
        public string|null $from_ip = null,
        public bool $can_delete = true,
    ) {}
}
