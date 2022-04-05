<?php declare(strict_types=1);

namespace App\States\Daemons;

class Restarting extends DaemonState
{
    public static string $name = 'restarting';

    public static bool $loading = true;
}
