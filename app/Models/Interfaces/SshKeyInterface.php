<?php declare(strict_types=1);

namespace App\Models\Interfaces;

use phpseclib3\Crypt\Common\PrivateKey;
use phpseclib3\Crypt\Common\PublicKey;

/**
 * SSH Key Interface for Eloquent models
 *
 * @property PublicKey $publicKey
 * @property PrivateKey|null $privateKey
 *
 * @property-read string $publicKeyString
 * @property-read string|null $privateKeyString
 * @property-read string $publicKeyFingerprint
 */
interface SshKeyInterface
{
    public function getPublicKeyAttribute(): PublicKey;

    public function getPrivateKeyAttribute(): PrivateKey|null;

    public function getPublicKeyString(bool $comment = true): string;

    public function getPrivateKeyString(bool $comment = true): string|null;

    public function getPublicKeyStringAttribute(): string;

    public function getPrivateKeyStringAttribute(): string|null;

    public function getPublicKeyFingerprintAttribute(): string;
}
