<?php declare(strict_types=1);

namespace App\States\Workers;

class Failed extends WorkerState
{
    public static string $name = 'failed';
    public static string $colors = 'danger';
}
