<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class DaemonData extends DataTransferObject
{
    public string $command;
    public string $username;
    public string|null $directory;
    public int $processes;
    public int $start_seconds;
}
