<?php declare(strict_types=1);

namespace App\States\Servers;

class Configuring extends ServerState
{
    public static string $name = 'configuring';

    public static bool $loading = true;
}
