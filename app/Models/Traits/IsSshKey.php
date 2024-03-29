<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Facades\Encryption;
use App\Support\SshKeyFormatter;
use Illuminate\Database\Eloquent\Model;
use phpseclib3\Crypt\Common\PrivateKey as PrivateKeyInterface;
use phpseclib3\Crypt\Common\PublicKey as PublicKeyInterface;
use phpseclib3\Crypt\PublicKeyLoader;

/**
 * Trait IsSshKey for Eloquent models
 *
 * @property PublicKeyInterface $publicKey
 * @property PrivateKeyInterface|null $privateKey
 *
 * @property-read string $publicKeyString
 * @property-read string|null $privateKeyString
 *
 * @mixin Model
 */
trait IsSshKey
{
    protected function hasPrivateKey(): bool
    {
        return true;
    }

    public function getPublicKeyAttribute(): PublicKeyInterface
    {
        /** @var PublicKeyInterface $publicKey */
        $publicKey = PublicKeyLoader::load($this->attributes['public_key']);

        return $publicKey;
    }

    public function setPublicKeyAttribute(PublicKeyInterface|PrivateKeyInterface $publicKey): void
    {
        if ($publicKey instanceof PrivateKeyInterface)
            $publicKey = $publicKey->getPublicKey();

        $this->attributes['public_key'] = $this->keyToString($publicKey, false);
    }

    public function getPrivateKeyAttribute(): PrivateKeyInterface|null
    {
        if (! $this->hasPrivateKey())
            return null;

        /** @var PrivateKeyInterface $privateKey */
        $privateKey = PublicKeyLoader::load(
            Encryption::decryptString($this->attributes['private_key'])
        );

        return $privateKey;
    }

    public function setPrivateKeyAttribute(PrivateKeyInterface $privateKey): void
    {
        if (! $this->hasPrivateKey())
            return;

        $this->attributes['private_key'] = Encryption::encryptString(
            $this->keyToString($privateKey, false)
        );
    }

    public function getPublicKeyString(bool $comment = true): string
    {
        return $this->keyToString($this->publicKey, $comment);
    }

    public function getPrivateKeyString(bool $comment = true): string|null
    {
        return $this->hasPrivateKey()
            ? $this->keyToString($this->privateKey, $comment)
            : null;
    }

    public function getPublicKeyStringAttribute(): string
    {
        return $this->getPublicKeyString();
    }

    public function getPrivateKeyStringAttribute(): string|null
    {
        return $this->getPrivateKeyString();
    }

    public function getPublicKeyFingerprintAttribute(): string
    {
        return $this->publicKey->getFingerprint('md5');
    }

    /** Convert a key to an OpenSSH formatted string with a proper comment included. */
    protected function keyToString(PrivateKeyInterface|PublicKeyInterface $key, bool $comment = true): string
    {
        return SshKeyFormatter::format($key, $comment ? $this->getSshKeyComment() : null);
    }

    /** Get a comment for an OpenSSH key. */
    abstract protected function getSshKeyComment(): string;
}
