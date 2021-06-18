<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\User;
use Spatie\DataTransferObject\DataTransferObject;
use Laravel\Socialite\Contracts\User as OAuthUser;
use App\Exceptions\NotImplementedException;
use RuntimeException;

class VcsProviderData extends DataTransferObject
{
    public User $user;
    public string $provider;
    public string $external_id;
    public string $nickname;
    public string|null $token;
    public string|null $key;
    public string|null $secret;

    public static function fromOauth(OAuthUser $oauthUser, string $providerName, User $user): static
    {
        return match ($providerName) {
            'github' => static::github($oauthUser, $user),
            'gitlab' => static::gitlab($oauthUser, $user),
            'bitbucket' => static::bitbucket($oauthUser, $user),
            default => throw new RuntimeException('Unknown VCS provider name.')
        };
    }

    private static function github(OAuthUser $oauthUser, User $user): static
    {
        return new static(
            user: $user,
            provider: 'github',
            external_id: (string) $oauthUser->getId(),
            nickname: $oauthUser->getNickname(),
            token: (string) $oauthUser->token,
        );
    }

    private static function gitlab(OAuthUser $oauthUser, User $user): static
    {
        // TODO: CRITICAL! Implement.

        throw new NotImplementedException;

        //
    }

    private static function bitbucket(OAuthUser $oauthUser, User $user): static
    {
        // TODO: CRITICAL! Implement.

        throw new NotImplementedException;

        //
    }
}
