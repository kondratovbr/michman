<?php declare(strict_types=1);

namespace App\States\Daemons;

class Failed extends DaemonState
{
    public static string $name = 'failed';
    public static string $colors = 'danger';
}
