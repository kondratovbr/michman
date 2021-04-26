<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\Provider;
use Spatie\DataTransferObject\DataTransferObject;

class NewServerData extends DataTransferObject
{
    public Provider $provider;
    public string $name;
    public string $region;
    public string $size;
    public string $type;
    public string|null $pythonVersion = null;
    public string $database = 'none';
    public string|null $dbName = null;
    public string $cache = 'none';
    public bool $addSshKeysToVcs = false;
}
