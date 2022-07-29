<?php declare(strict_types=1);

namespace App\Scripts\Traits;

trait InteractsWithUfw
{
    /**
     * Create a full UFW rule string.
     */
    protected function ufwRule(string $port, string $type, string|null $fromIp = null): string
    {
        // Rule syntax example:
        // allow from 192.168.0.4 to any port 66 proto tcp

        if (empty($fromIp))
            $fromIp = 'any';

        return "$type from $fromIp to any port $port proto tcp";
    }
}
