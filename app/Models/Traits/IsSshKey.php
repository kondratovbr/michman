<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
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
            Crypt::decryptString($this->attributes['private_key'])
        );

        return $privateKey;
    }

    public function setPrivateKeyAttribute(PrivateKeyInterface $privateKey): void
    {
        if (! $this->hasPrivateKey())
            return;

        $this->attributes['private_key'] = Crypt::encryptString(
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

    /**
     * Convert a key to an OpenSSH formatted string with a proper comment included.
     */
    protected function keyToString(PrivateKeyInterface|PublicKeyInterface $key, bool $comment = true): string
    {
        // toString() method on a public key in phpseclib3 adds a space at the end
        // if the comment provided is an empty string, for some reason, so better trim it.
        return trim(
            $comment
                ? $key->toString('OpenSSH', ['comment' => $this->getSshKeyComment()])
                : $key->toString('OpenSSH', ['comment' => '']),
            ' '
        );
    }

    /**
     * Get a comment for an OpenSSH key.
     */
    abstract protected function getSshKeyComment(): string;
}
