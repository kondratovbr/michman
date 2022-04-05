<?php declare(strict_types=1);

namespace App\States\Daemons;

class Starting extends DaemonState
{
    public static string $name = 'starting';

    public static bool $loading = true;
}
