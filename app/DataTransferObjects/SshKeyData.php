<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class SshKeyData extends DataTransferObject
{
    public string|null $id;
    public string|null $fingerprint;
    public string $publicKey;
    public string $name;
}
