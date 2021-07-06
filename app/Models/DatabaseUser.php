<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\DatabaseUserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * DatabaseUser Eloquent model
 *
 * @property int $id
 * @property string $name
 * @property string|null $password
 * @property string $status
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 *
 * @property-read Server $server
 * @property-read Collection $databases
 *
 * @method static DatabaseUserFactory factory(...$parameters)
 */
class DatabaseUser extends AbstractModel
{
    use HasFactory;

    public const STATUS_CREATED = 'created';
    public const STATUS_CREATING = 'creating';
    public const STATUS_UPDATING = 'updating';
    public const STATUS_DELETING = 'deleting';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'password',
        'status',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast. */
    protected $casts = [
        'password' => 'encrypted',
    ];

    /**
     * Get the current status of this database user.
     */
    public function getStatusAttribute(): string
    {
        return $this->attributes['status'] ?? static::STATUS_CREATING;
    }

    /**
     * Get the user who owns the server with this database user.
     */
    public function getUserAttribute(): User
    {
        return $this->server->provider->owner;
    }

    /**
     * Determine if this database user was created on the server.
     */
    public function isCreated(): bool
    {
        return $this->status === static::STATUS_CREATED;
    }

    /**
     * Determine if this database user is in the state of being created on the server.
     */
    public function isCreating(): bool
    {
        return $this->status === static::STATUS_CREATING;
    }

    /**
     * Determine if this database user is in the process of being updated on the server.
     */
    public function isUpdating(): bool
    {
        return $this->status === static::STATUS_UPDATING;
    }

    /**
     * Determine if this database user is in the state of being deleted from the server.
     */
    public function isDeleting(): bool
    {
        return $this->status === static::STATUS_DELETING;
    }

    /**
     * Get a relation with the server where this database user is created.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get a relation with the databases this database user can access.
     */
    public function databases(): BelongsToMany
    {
        return $this->belongsToMany(Database::class, 'database_database_user')
            ->withTimestamps();
    }
}
