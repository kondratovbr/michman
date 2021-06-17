<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\User;
use Spatie\DataTransferObject\DataTransferObject;

class VcsProviderData extends DataTransferObject
{
    public User $user;
    public string $provider;
    public string $externalId;
    public string|null $token;
    public string|null $key;
    public string|null $secret;
}
