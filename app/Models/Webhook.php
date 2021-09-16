<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Webhooks\WebhookCreatedEvent;
use App\Events\Webhooks\WebhookDeletedEvent;
use App\Events\Webhooks\WebhookUpdatedEvent;
use App\Models\Traits\HasStatus;
use App\Models\Traits\UsesUuid;
use Carbon\CarbonInterface;
use Database\Factories\WebhookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Webhook Eloquent model
 *
 * @property string $id
 * @property string $type
 * @property string|null $externalId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read string $payloadUrl
 *
 * @property-read Project $project
 *
 * @method static WebhookFactory factory(...$parameters)
 */
class Webhook extends AbstractModel
{
    use HasFactory;
    use HasStatus;
    use UsesUuid;

    public const STATUS_ENABLING = 'enabling';
    public const STATUS_ENABLED = 'enabled';
    public const STATUS_DISABLING = 'disabling';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'status',
        'type',
        'external_id',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        //
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => WebhookCreatedEvent::class,
        'updated' => WebhookUpdatedEvent::class,
        'deleted' => WebhookDeletedEvent::class,
    ];

    /** Generate a URL that the external service should be sending payload to. */
    public function getPayloadUrlAttribute(): string
    {
        return route('hook.push', [$this->project->vcsProvider->webhookProvider, $this]);
    }

    public function enabled(): bool
    {
        return $this->isStatus(static::STATUS_ENABLED);
    }

    /** Get a relation with the project that this webhook is attached to. */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
