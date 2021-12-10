<?php declare(strict_types=1);

namespace App\States\Servers;

class Creating extends ServerState
{
    public static string $name = 'creating';

    public static bool $loading = true;
}
