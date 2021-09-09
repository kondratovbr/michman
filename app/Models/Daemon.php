<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Facades\ConfigView;
use App\Models\Traits\HasStatus;
use App\Support\Arr;
use Carbon\CarbonInterface;
use Database\Factories\DaemonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Daemon Eloquent model
 *
 * Represents a supervisord-managed program running on a server.
 *
 * @property int $id
 * @property string $command
 * @property string $username
 * @property string|null $directory
 * @property int $processes
 * @property int $startSeconds
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 * @property-read string $name
 *
 * @property-read Server $server
 *
 * @method static DaemonFactory factory(...$parameters)
 */
class Daemon extends AbstractModel
{
    use HasFactory,
        HasStatus;

    public const STATUS_STARTING = 'starting';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_STOPPING = 'stopping';
    public const STATUS_STOPPED = 'stopped';
    public const STATUS_FAILED = 'failed';
    public const STATUS_DELETING = 'deleting';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'status',
        'command',
        'username',
        'directory',
        'processes',
        'start_seconds',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        //
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => DaemonCreatedEvent::class,
        'updated' => DaemonUpdatedEvent::class,
        'deleted' => DaemonDeletedEvent::class,
    ];

    public function getUserAttribute(): User
    {
        return $this->server->user;
    }

    /**
     * Derive the name for this daemon from its properties.
     */
    public function getNameAttribute(): string
    {
        return "daemon-{$this->id}";
    }

    public function isStarting(): bool
    {
        return $this->status === static::STATUS_STARTING;
    }

    public function isActive(): bool
    {
        return $this->status === static::STATUS_ACTIVE;
    }

    public function isStopped(): bool
    {
        return $this->status === static::STATUS_STOPPED;
    }

    public function isFailed(): bool
    {
        return $this->status === static::STATUS_FAILED;
    }

    /**
     * Get the path to the Supervisor config for this daemon on a server.
     */
    public function configPath(): string
    {
        return "/etc/supervisor/conf.d/{$this->name}.conf";
    }

    /**
     * Get the path to the file where this daemon stores logs on a server.
     */
    public function logFilePath(): string
    {
        return "/var/log/michman/{$this->name}.log";
    }

    /**
     * Create a supervisord config for this daemon.
     */
    public function supervisorConfig(): string
    {
        return ConfigView::render('supervisor.daemon', [
            'daemon' => $this,
            'server' => $this->server,
        ]);
    }

    /**
     * Get a relation with the server where this daemon is running.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
