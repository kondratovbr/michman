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
        public OAuthTokenDto|null $token = null,
        public string|null $key = null,
        public string|null $secret = null,
    ) {}

    /** Convert this DTO to VcsProvider model attributes. */
    public function toAttributes(): array
    {
        $attrs = $this->except('token')->toArray();

        if (! is_null($this->token))
            $attrs = Arr::merge($attrs, $this->token->toArray());

        return $attrs;
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
            token: OAuthTokenDto::fromData($oauthUser->token),
        );
    }

    private static function gitlab(OAuthUser $oauthUser): static
    {
        return new static(
            provider: 'gitlab_v4',
            external_id: (string) $oauthUser->getId(),
            nickname: $oauthUser->getNickname(),
            token: OAuthTokenDto::fromData(
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
            token: OAuthTokenDto::fromData($oauthUser->token),
        );
    }
}
