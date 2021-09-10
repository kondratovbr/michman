<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Exceptions\NotImplementedException;
use App\Models\User;
use Laravel\Socialite\Contracts\User as OAuthUser;
use RuntimeException;

class VcsProviderDto extends AbstractDto
{
    public function __construct(
        public User $user,
        public string $provider,
        public string $external_id,
        public string $nickname,
        public string|null $token = null,
        public string|null $key = null,
        public string|null $secret = null,
    ) {}

    public static function fromOauth(OAuthUser $oauthUser, string $vcsProviderName, User $user): static
    {
        return match ($vcsProviderName) {
            'github_v3' => static::github($oauthUser, $user),
            'gitlab' => static::gitlab($oauthUser, $user),
            'bitbucket' => static::bitbucket($oauthUser, $user),
            default => throw new RuntimeException('Unknown VCS provider name.')
        };
    }

    private static function github(OAuthUser $oauthUser, User $user): static
    {
        return new static(
            user: $user,
            provider: 'github_v3',
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
