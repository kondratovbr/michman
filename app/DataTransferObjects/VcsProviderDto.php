<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Support\Arr;
use Laravel\Socialite\Contracts\User as OAuthUser;
use RuntimeException;

class VcsProviderDto extends AbstractDto
{
    public function __construct(
        public string $provider,
        public string $external_id,
        public string $nickname,
        public AuthTokenDto|null $token,
    ) {}

    /** Convert this DTO to VcsProvider model attributes. */
    public function toAttributes(array $add = []): array
    {
        return $this->except('token')->toArray(Arr::merge($add, [
            'token' => $this->token,
        ]));
    }

    public static function fromOauth(OAuthUser $oauthUser, string $vcsProviderName): static
    {
        return match ($vcsProviderName) {
            'github_v3' => static::github($oauthUser),
            'gitlab_v4' => static::gitlab($oauthUser),
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
            token: AuthTokenDto::fromData(
                (string) $oauthUser->getId(),
                $oauthUser->token,
            ),
        );
    }

    private static function gitlab(OAuthUser $oauthUser): static
    {
        return new static(
            provider: 'gitlab_v4',
            external_id: (string) $oauthUser->getId(),
            nickname: $oauthUser->getNickname(),
            token: AuthTokenDto::fromData(
                (string) $oauthUser->getId(),
                $oauthUser->token,
                $oauthUser->refreshToken,
                $oauthUser->expiresIn,
            ),
        );
    }

    private static function bitbucket(OAuthUser $oauthUser): static
    {
        return new static(
            provider: 'bitbucket',
            external_id: (string) $oauthUser->getId(),
            nickname: $oauthUser->getNickname(),
            token: AuthTokenDto::fromData(
                (string) $oauthUser->getId(),
                $oauthUser->token,
            ),
        );
    }
}
