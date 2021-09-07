<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
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
 * @property string $status
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 *
 * @property-read Server $server
 *
 * @method static DaemonFactory factory(...$parameters)
 */
class Daemon extends AbstractModel
{
    use HasFactory;

    public const STATUS_STARTING = 'starting';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_FAILED = 'failed';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'command',
        'username',
        'directory',
        'processes',
        'start_seconds',
        'status',
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
     * Get a relation with the server where this daemon is running.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
