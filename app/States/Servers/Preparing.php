<?php declare(strict_types=1);

namespace App\States\Servers;

class Preparing extends ServerState
{
    public static string $name = 'preparing';

    public static bool $loading = true;
}
