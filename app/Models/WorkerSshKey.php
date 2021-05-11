<?php declare(strict_types=1);

namespace App\Models;

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
 * @property PublicKeyInterface $publicKey
 * @property PrivateKeyInterface $privateKey
 * @property string $name
 * @property string|null $externalId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read Server $server
 *
 * @method static WorkerSshKeyFactory factory(...$parameters)
 */
class WorkerSshKey extends AbstractModel
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

        $this->attributes['public_key'] = $publicKey
            ->toString('OpenSSH', ['comment' => $this->server->name]);
    }

    public function getPrivateKeyAttribute(): PrivateKeyInterface
    {
        /** @var PrivateKeyInterface $privateKey */
        $privateKey = PublicKeyLoader::load(Crypt::decryptString($this->attributes['private_key']));

        return $privateKey;
    }

    public function setPrivateKeyAttribute(PrivateKeyInterface $privateKey): void
    {
        $this->attributes['private_key'] = Crypt::encryptString(
            $privateKey->toString('OpenSSH', ['comment' => $this->server->name])
        );
    }

    /**
     * Get a relation with the server that uses this key.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
