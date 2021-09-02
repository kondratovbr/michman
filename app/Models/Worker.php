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
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
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
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        //
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => WorkerCreatedEvent::class,
        'updated' => WorkerUpdatedEvent::class,
        'deleted' => WorkerDeletedEvent::class,
    ];

    /**
     * Get a relation with the server this worker is set up on.
     */
    public function servers(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
