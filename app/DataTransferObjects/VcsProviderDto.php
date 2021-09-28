<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Exceptions\NotImplementedException;
use Laravel\Socialite\Contracts\User as OAuthUser;
use RuntimeException;

class VcsProviderDto extends AbstractDto
{
    public function __construct(
        public string $provider,
        public string $external_id,
        public string $nickname,
        public string|null $token = null,
        public string|null $key = null,
        public string|null $secret = null,
    ) {}

    public static function fromOauth(OAuthUser $oauthUser, string $vcsProviderName): static
    {
        return match ($vcsProviderName) {
            'github_v3' => static::github($oauthUser),
            'gitlab' => static::gitlab($oauthUser),
            'bitbucket' => static::bitbucket($oauthUser),
            default => throw new RuntimeException('Unknown VCS provider name.')
        };
    }

    private static function github(OAuthUser $oauthUser): static
    {
        return new static(
            provider: 'github_v3',
            external_id: (string) $oauthUser->getId(),
            nickname: $oauthUser->getNickname(),
            token: (string) $oauthUser->token,
        );
    }

    private static function gitlab(OAuthUser $oauthUser): static
    {
        // TODO: CRITICAL! Implement. And add a test for it.

        throw new NotImplementedException;

        //
    }

    private static function bitbucket(OAuthUser $oauthUser): static
    {
        // TODO: CRITICAL! Implement. And add a test for it.

        throw new NotImplementedException;

        //
    }
}
