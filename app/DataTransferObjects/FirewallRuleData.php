<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\Server;
use Spatie\DataTransferObject\DataTransferObject;

class FirewallRuleData extends DataTransferObject
{
    public string $name;
    public string $port;
    public string|null $fromIp = null;
}
