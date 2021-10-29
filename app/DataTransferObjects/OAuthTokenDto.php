<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Carbon\CarbonInterface;

class OAuthTokenDto extends AbstractDto
{
    public function __construct(
        public string $token,
        public string|null $refresh_token = null,
        public CarbonInterface|null $expires_at = null,
    ) {}

    public static function fromData(
        string $token,
        string|null $refreshToken = null,
        int|null $expiresInSecs = null,
    ): static {
        return new static(
            token: $token,
            refresh_token: $refreshToken,
            expires_at: now()->addSeconds((int) floor($expiresInSecs * 0.9)),
        );
    }
}
