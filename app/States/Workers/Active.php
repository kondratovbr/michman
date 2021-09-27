<?php declare(strict_types=1);

namespace App\States\Workers;

class Active extends WorkerState
{
    public static string $name = 'active';
    public static string $colors = 'success';
}
