<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Facades\ConfigView;
use App\Models\Traits\HasStatus;
use App\States\Daemons\DaemonState;
use Carbon\CarbonInterface;
use Database\Factories\DaemonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\ModelStates\HasStates;

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
 * @property DaemonState $state
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
    use HasFactory;
    use HasStates;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'command',
        'username',
        'directory',
        'processes',
        'start_seconds',
        'state',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'state' => DaemonState::class,
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
