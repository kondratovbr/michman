<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\User;

class ProviderData extends AbstractData
{
    public User $owner;
    public string $provider;
    public string|null $token;
    public string|null $key;
    public string|null $secret;
    public string|null $name;
}
