<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Daemon Eloquent model
 *
 * Represents a supervisord-managed program running on a server.
 *
 * @property int $id
 * @property string $command
 * @property string $user
 * @property string|null $directory
 * @property int $processes
 * @property int $start_seconds
 * @property CarbonInterface $c
 */
class Daemon extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'command',
        'user',
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
}
