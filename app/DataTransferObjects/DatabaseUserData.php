<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class DatabaseUserData extends DataTransferObject
{
    public string $name;
    public string $password;
}
