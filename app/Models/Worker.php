<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Workers\WorkerCreatedEvent;
use App\Events\Workers\WorkerDeletedEvent;
use App\Events\Workers\WorkerUpdatedEvent;
use Carbon\CarbonInterface;
use Database\Factories\WorkerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Worker Eloquent model
 *
 * Represents a server queue worker.
 *
 * @property int $id
 * @property string $type
 * @property string|null $app
 * @property int $processes
 * @property array $queues
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 * @property-read string $debugLevel
 * @property-read string $name
 *
 * @property-read Project $project
 * @property-read Server $server
 *
 * @method static WorkerFactory factory(...$parameters)
 */
class Worker extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'type',
        'app',
        'processes',
        'queues',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'queues' => 'array',
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => WorkerCreatedEvent::class,
        'updated' => WorkerUpdatedEvent::class,
        'deleted' => WorkerDeletedEvent::class,
    ];

    /**
     * Get the user who owns this worker.
     */
    public function getUserAttribute(): User
    {
        return $this->project->user;
    }

    /**
     * Derive the application name for this worker from the project,
     * if it wasn't set explicitly.
     */
    public function getAppAttribute(): string
    {
        return $this->attributes['app'] ?? $this->project->package;
    }

    /**
     * Get a desired debug level for this queue worker.
     */
    public function getDebugLevelAttribute(): string
    {
        return 'INFO';
    }

    /**
     * Derive a name for this worker from its properties.
     */
    public function getNameAttribute(): string
    {
        // TODO: CRITICAL! Don't forget this. Very important - celery instances running on the same machine must have different names explicitly set up.
        return "worker-{$this->id}";
    }

    /**
     * Get a relation with the project this worker is configured for.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get a relation with the server this worker is set up on.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
