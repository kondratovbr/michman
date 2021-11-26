<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Carbon\CarbonInterface;

class AuthTokenDto extends AbstractDto
{
    public function __construct(
        public string|null $id,
        public string $token,
        public string|null $refresh_token = null,
        public CarbonInterface|null $expires_at = null,
    ) {}

    public static function fromData(
        string|null $id,
        string $token,
        string|null $refreshToken = null,
        int|string|null $expiresInSecs = null,
    ): static {
        return new static(
            id: $id,
            token: $token,
            refresh_token: $refreshToken,
            expires_at: now()->addSeconds((int) floor((int) $expiresInSecs * 0.9)),
        );
    }

    /** Check if this token can expire (has expiration time set). */
    public function canExpire(): bool
    {
        return isset($this->expires_at);
    }

    /** Check if this token has expired. */
    public function expired(): bool
    {
        if (is_null($this->expires_at))
            return false;

        return $this->expires_at->lessThanOrEqualTo(now());
    }
}
