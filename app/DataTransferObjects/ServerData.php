<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ServerData extends DataTransferObject
{
    public string $id;
    public string $name;
    public string $ip;

    //
}
