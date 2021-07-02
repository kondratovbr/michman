<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class DatabaseData extends DataTransferObject
{
    public string $name;
}
