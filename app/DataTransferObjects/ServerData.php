<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ServerData extends DataTransferObject
{
    public string $id;
    public string $name;
    // IP can be null if it wasn't yet attached to the server by the provider.
    public string|null $publicIp4;

    //
}
