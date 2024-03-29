<?php declare(strict_types=1);

namespace App\Models;

use App\Events\UserSshKeys\UserSshKeyCreatedEvent;
use App\Events\UserSshKeys\UserSshKeyDeletedEvent;
use App\Models\Interfaces\SshKeyInterface;
use App\Models\Traits\IsSshKey;
use Carbon\CarbonInterface;
use Database\Factories\UserSshKeyFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * UserSshKey Eloquent model
 *
 * Represents an SSH key that the user added to their account to be able to access their servers manually.
 *
 * @property int $id
 *
 * IDs
 * @property int $userId
 *
 * Properties
 * @property string $name
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * Custom attributes
 * @property-read null $privateKey
 * @property-read null $privateKeyString
 *
 * Relations
 * @property-read User $user
 * @property-read Collection $servers
 *
 * @method static UserSshKeyFactory factory(...$parameters)
 *
 * @mixin IdeHelperUserSshKey
 */
class UserSshKey extends AbstractModel implements SshKeyInterface
{
    use HasFactory;
    use IsSshKey;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => UserSshKeyCreatedEvent::class,
        'deleted' => UserSshKeyDeletedEvent::class,
    ];

    protected function hasPrivateKey(): bool
    {
        return false;
    }

    protected function getSshKeyComment(): string
    {
        return $this->name;
    }

    /** Check if this key is added to all the servers it's owner has. */
    public function addedToAllServers(): bool
    {
        return $this->servers()->count() == $this->user->servers()->count();
    }

    /** Get a relation with the user who owns this key. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Get a relation with the server that owns this key. */
    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'server_user_ssh_key')
            ->using(ServerUserSshKeyPivot::class)
            ->withTimestamps();
    }
}
