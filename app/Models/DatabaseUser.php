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
 * @property string $status
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
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

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /**
     * Get the current status of this database user.
     */
    public function getStatusAttribute(): string
    {
        return $this->attributes['status'] ?? static::STATUS_CREATING;
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
