<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\SshKeyInterface;
use Carbon\CarbonInterface;
use Database\Factories\DeploySshKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use phpseclib3\Crypt\Common\PrivateKey as PrivateKeyInterface;
use phpseclib3\Crypt\Common\PublicKey as PublicKeyInterface;
use phpseclib3\Crypt\PublicKeyLoader;

/**
 * DeploySshKey Eloquent model
 *
 * Represents an SSH key that was automatically created for a server
 * and added to a specific VCS repository to be used for deployment.
 *
 * @property int $id
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read Server $server
 *
 * @method static DeploySshKeyFactory factory(...$parameters)
 */
class DeploySshKey extends AbstractModel implements SshKeyInterface
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        //
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

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

        $this->attributes['public_key'] = $this->keyToString($publicKey);
    }

    public function getPrivateKeyAttribute(): PrivateKeyInterface
    {
        /** @var PrivateKeyInterface $privateKey */
        $privateKey = PublicKeyLoader::load(Crypt::decryptString($this->attributes['private_key']));

        return $privateKey;
    }

    public function setPrivateKeyAttribute(PrivateKeyInterface $privateKey): void
    {
        $this->attributes['private_key'] = Crypt::encryptString($this->keyToString($privateKey));
    }

    public function getPublicKeyStringAttribute(): string
    {
        return $this->keyToString($this->privateKey);
    }

    public function getPrivateKeyStringAttribute(): string
    {
        return $this->keyToString($this->publicKey);
    }

    /**
     * Convert a key to an OpenSSH formatted string with a proper comment included.
     */
    protected function keyToString(PrivateKeyInterface|PublicKeyInterface $key): string
    {
        return $key->toString('OpenSSH', ['comment' => $this->server->name . ' - deploy key']);
    }

    // TODO: CRITICAL! This is a mistake. A deploy key should be owned by a project and attached to many servers - all of the servers where the project is deployed. Fix and don't forget the migrations and the inverse part of this relation. The comment in keyToString is also not correct - should include project name.
    /**
     * Get a relation with the server that has this key.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
