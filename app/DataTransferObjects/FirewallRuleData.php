<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class FirewallRuleData extends DataTransferObject
{
    public string $name;
    public string $port;
    public string|null $from_ip = null;
    public bool $can_delete = true;
}
