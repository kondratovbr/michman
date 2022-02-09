<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Databases\DatabaseCreatedEvent;
use App\Events\Databases\DatabaseDeletedEvent;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Models\Interfaces\HasTasksCounterInterface;
use App\Models\Traits\HasTasksCounter;
use Carbon\CarbonInterface;
use Database\Factories\DatabaseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Database Eloquent model
 *
 * @property int $id
 * @property int $serverId
 *
 * @property string $name
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 *
 * @property-read Server $server
 * @property-read Collection $databaseUsers
 * @property-read Project|null $project
 *
 * @method static DatabaseFactory factory(...$parameters)
 */
class Database extends AbstractModel implements HasTasksCounterInterface
{
    use HasFactory,
        HasTasksCounter;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => DatabaseCreatedEvent::class,
        'updated' => DatabaseUpdatedEvent::class,
        'deleted' => DatabaseDeletedEvent::class,
    ];

    /** Get the user who owns a server with this database. */
    protected function getUserAttribute(): User
    {
        return $this->server->user;
    }

    /** Get a relation with the server that holds this database. */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /** Get a relation with the database users that have access to this database. */
    public function databaseUsers(): BelongsToMany
    {
        return $this->belongsToMany(DatabaseUser::class, 'database_database_user')
            ->withTimestamps();
    }

    /** Get a relation with the project that uses this database, if any. */
    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    public function delete(): bool|null
    {
        $this->databaseUsers()->detach();
        $this->project?->database()->disassociate();

        return parent::delete();
    }
}
