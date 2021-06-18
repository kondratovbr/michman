<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\User;
use Spatie\DataTransferObject\DataTransferObject;

class VcsProviderData extends DataTransferObject
{
    public User $user;
    public string $provider;
    public string $external_id;
    public string $nickname;
    public string|null $token;
    public string|null $key;
    public string|null $secret;
}
