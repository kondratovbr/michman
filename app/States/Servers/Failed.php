<?php declare(strict_types=1);

namespace App\States\Servers;

class Failed extends ServerState
{
    public static string $name = 'failed';

    public static string $colors = 'danger';
}
