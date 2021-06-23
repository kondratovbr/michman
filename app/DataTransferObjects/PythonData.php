<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\Server;
use Spatie\DataTransferObject\DataTransferObject;

class PythonData extends DataTransferObject
{
    public Server $server;
    public string $version;
}
