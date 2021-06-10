<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\SshKeyInterface;
use Carbon\CarbonInterface;
use Database\Factories\WorkerSshKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use phpseclib3\Crypt\Common\PrivateKey as PrivateKeyInterface;
use phpseclib3\Crypt\Common\PublicKey as PublicKeyInterface;
use phpseclib3\Crypt\PublicKeyLoader;

/**
 * WorkerSshKey Eloquent model
 *
 * Represents an SSH key that our worker uses to access the server.
 *
 * @property int $id
 * @property string $name
 * @property string|null $externalId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read Server $server
 *
 * @method static WorkerSshKeyFactory factory(...$parameters)
 */
class WorkerSshKey extends AbstractModel implements SshKeyInterface
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'external_id',
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

    public function getPrivateKeyStringAttribute(): string
    {
        return $this->keyToString($this->privateKey);
    }

    public function getPublicKeyStringAttribute(): string
    {
        return $this->keyToString($this->publicKey);
    }

    /**
     * Convert a key to an OpenSSH formatted string with a proper comment included.
     */
    protected function keyToString(PrivateKeyInterface|PublicKeyInterface $key): string
    {
        return $key->toString('OpenSSH', ['comment' => $this->server->name]);
    }

    /**
     * Get a relation with the server that uses this key.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
