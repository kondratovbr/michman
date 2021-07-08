<?php declare(strict_types=1);

namespace App\Models;

use App\Events\DatabaseUsers\DatabaseUserCreatedEvent;
use App\Events\DatabaseUsers\DatabaseUserDeletedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Models\Interfaces\HasTasksCounterInterface;
use App\Models\Traits\HasTasksCounter;
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
class DatabaseUser extends AbstractModel implements HasTasksCounterInterface
{
    use HasFactory,
        HasTasksCounter;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'password',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast. */
    protected $casts = [
        'password' => 'encrypted',
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => DatabaseUserCreatedEvent::class,
        'updated' => DatabaseUserUpdatedEvent::class,
        'deleted' => DatabaseUserDeletedEvent::class,
    ];

    /**
     * Get the user who owns the server with this database user.
     */
    public function getUserAttribute(): User
    {
        return $this->server->provider->owner;
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
