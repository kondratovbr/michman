<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\DatabaseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Database Eloquent model
 *
 * @property int $id
 * @property string $name
 * @property string $status
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 *
 * @property-read Server $server
 * @property-read Collection $databaseUsers
 *
 * @method static DatabaseFactory factory(...$parameters)
 */
class Database extends AbstractModel
{
    use HasFactory;

    public const STATUS_CREATED = 'created';
    public const STATUS_CREATING = 'creating';
    public const STATUS_DELETING = 'deleting';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'status',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /**
     * Get the current status of this database.
     */
    public function getStatusAttribute(): string
    {
        return $this->attributes['status'] ?? static::STATUS_CREATING;
    }

    /**
     * Get the user who owns a server with this database.
     */
    public function getUserAttribute(): User
    {
        return $this->server->user;
    }

    /**
     * Determine if this database was created on the server.
     */
    public function isCreated(): bool
    {
        return $this->status === static::STATUS_CREATED;
    }

    /**
     * Determine if this database is in the process of being created on the server.
     */
    public function isCreating(): bool
    {
        return $this->status === static::STATUS_CREATING;
    }

    /**
     * Determine if this database is in the process of being deleted from the server.
     */
    public function isDeleting(): bool
    {
        return $this->status === static::STATUS_DELETING;
    }

    /**
     * Get a relation with the server that holds this database.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get a relation with the database users that have access to this database.
     */
    public function databaseUsers(): BelongsToMany
    {
        return $this->belongsToMany(DatabaseUser::class, 'database_database_user')
            ->withTimestamps();
    }
}
