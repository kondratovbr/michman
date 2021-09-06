<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Workers\WorkerCreatedEvent;
use App\Events\Workers\WorkerDeletedEvent;
use App\Events\Workers\WorkerUpdatedEvent;
use App\Facades\ConfigView;
use Carbon\CarbonInterface;
use Database\Factories\WorkerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * Worker Eloquent model
 *
 * Represents a server queue worker.
 *
 * @property int $id
 * @property string $status
 * @property string $type
 * @property string|null $app
 * @property int|null $processes
 * @property array|null $queues
 * @property int|null $stopSeconds
 * @property int|null $maxTasksPerChild
 * @property int|null $maxMemoryPerChild
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

    public const STATUS_STARTING = 'starting';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DELETING = 'deleting';
    public const STATUS_FAILED = 'failed';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'status',
        'type',
        'app',
        'processes',
        'queues',
        'stop_seconds',
        'max_tasks_per_child',
        'max_memory_per_child',
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
        return "worker-{$this->id}";
    }

    public function isStarting(): bool
    {
        return $this->status === static::STATUS_STARTING;
    }

    public function isActive(): bool
    {
        return $this->status === static::STATUS_ACTIVE;
    }

    public function isDeleting(): bool
    {
        return $this->status === static::STATUS_DELETING;
    }

    /**
     * Create a supervisord config for this worker
     */
    public function supervisorConfig(): string
    {
        return ConfigView::render(match ($this->type) {
            'celery' => 'supervisor.celery',
            'celerybeat' => 'supervisor.celerybeat',
            default => throw new RuntimeException('This Worker model\'s type is not supported.'),
        }, [
            'worker' => $this,
            'server' => $this->server,
            'project' => $this->project,
        ]);
    }

    /**
     * Get the path to the supervisord config file for this worker on a server.
     */
    public function configPath(): string
    {
        return "/etc/supervisord/conf.d/{$this->name}";
    }

    /**
     * Generate a command that will run this worker on a server.
     */
    public function command(): string
    {
        $app = $this->app ?? $this->project->package;

        // TODO: CRITICAL! Make sure to support other Python queue packages in here.
        if ($this->type === 'celery') {
            $concurrency = is_null($this->processes) ? null : "--concurrency={$this->processes}";
            $queues = empty($this->queues) ? null : '-Q=' . implode(',', $this->queues);
            $maxTasks = is_null($this->maxTasksPerChild) ? null : "--max-tasks-per-child={$this->maxTasksPerChild}";
            $maxMemory = is_null($this->maxMemoryPerChild) ? null : "--max-memory-per-child={$this->maxMemoryPerChild}";

            return "{$this->project->projectDir}/venv/bin/celery --app={$app} worker {$concurrency} {$queues} {$maxTasks} {$maxMemory} --loglevel=INFO -n {$this->name}";
        }

        if ($this->type === 'celerybeat') {
            return "{$this->project->projectDir}/venv/bin/celery --app={$app} beat --loglevel=INFO -n {$this->name}";
        }

        throw new \RuntimeException(
            'This Worker model doesn\'t support it\'s type.'
            . 'Worker ID: ' . ($this->id ?? '')
            . ', type: ' . ($this->type ?? '')
        );
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
