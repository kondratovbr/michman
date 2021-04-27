<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class SshKeyData extends DataTransferObject
{
    public string $id;
    public string $fingerprint;
    public string $publicKey;
    public string $name;
}
