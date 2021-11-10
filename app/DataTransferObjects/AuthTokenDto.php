<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Carbon\CarbonInterface;

/*
 * TODO: CRITICAL! CONTINUE. Finish implementing storing and retrieving encrypted token for VcsProvider DTOs, finish IsApiProvider trait and test VCS on GitHub3. Then - update GitLabV4. Then - update Provider and DigitalOceanV2.
 */

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
        int|null $expiresInSecs = null,
    ): static {
        return new static(
            id: $id,
            token: $token,
            refresh_token: $refreshToken,
            expires_at: now()->addSeconds((int) floor($expiresInSecs * 0.9)),
        );
    }
}
