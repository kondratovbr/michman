<?php declare(strict_types=1);

namespace App\States\Daemons;

class Deleting extends DaemonState
{
    public static string $name = 'deleting';

    public static bool $loading = true;
}
