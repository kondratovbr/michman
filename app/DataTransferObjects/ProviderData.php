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

    /**
     * Create a new ProviderData object from raw data.
     */
    public static function create(
        User $owner,
        string $provider,
        string|null $token = null,
        string|null $key = null,
        string|null $secret = null,
        string|null $name = null,
    ): self {
        return new self(
            owner: $owner,
            provider: $provider,
            token: $token,
            key: $key,
            secret: $secret,
            name: $name,
        );
    }
}
