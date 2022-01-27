<?php declare(strict_types=1);

namespace App\States\Servers;

class Deleting extends ServerState
{
    public static string $name = 'deleting';

    public static string $colors = 'danger';
    public static bool $loading = true;
}
