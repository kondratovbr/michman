<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\SshKeyInterface;
use Carbon\CarbonInterface;
use Database\Factories\UserSshKeyFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use phpseclib3\Crypt\Common\PrivateKey as PrivateKeyInterface;
use phpseclib3\Crypt\Common\PublicKey as PublicKeyInterface;
use phpseclib3\Crypt\PublicKeyLoader;

/**
 * UserSshKey Eloquent model
 *
 * @property int $id
 * @property string $username
 * @property string $name
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property null $privateKey
 * @property null $privateKeyString
 *
 * @property-read User $user
 * @property-read Collection $servers
 *
 * @method static UserSshKeyFactory factory(...$parameters)
 */
class UserSshKey extends AbstractModel implements SshKeyInterface
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'username',
        'public_key',
        'name',
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

    public function getPublicKeyStringAttribute(): string
    {
        return $this->keyToString($this->publicKey);
    }

    public function getPrivateKeyAttribute(): PrivateKeyInterface|null
    {
        return null;
    }

    public function getPrivateKeyStringAttribute(): string|null
    {
        return null;
    }

    /**
     * Convert a key to an OpenSSH formatted string with a proper comment included.
     */
    protected function keyToString(PrivateKeyInterface|PublicKeyInterface $key): string
    {
        return $key->toString('OpenSSH', ['comment' => $this->name]);
    }

    /**
     * Get a relation with the user who owns this key.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a relation with the server that owns this key.
     */
    public function server(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'server_user_ssh_key')
            ->using(ServerUserSshKeyPivot::class)
            ->withTimestamps();
    }
}
