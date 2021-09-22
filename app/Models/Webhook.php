<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Webhooks\WebhookCreatedEvent;
use App\Events\Webhooks\WebhookDeletedEvent;
use App\Events\Webhooks\WebhookUpdatedEvent;
use App\Models\Traits\UsesUuid;
use App\Services\Webhooks\WebhookServiceInterface;
use App\States\Webhooks\Deleting;
use App\States\Webhooks\Enabled;
use App\States\Webhooks\Enabling;
use App\States\Webhooks\WebhookState;
use Carbon\CarbonInterface;
use Database\Factories\WebhookFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Spatie\ModelStates\HasStates;

/**
 * Webhook Eloquent model
 *
 * @property int $id
 * @property string $uuid
 * @property string $provider
 * @property string $type
 * @property string $secret
 * @property string|null $externalId
 * @property WebhookState $state
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 * @property-read string $payloadUrl
 * @property-read string $repo
 *
 * @property-read Project $project
 * @property-read Collection $calls
 *
 * @method static WebhookFactory factory(...$parameters)
 */
class Webhook extends AbstractModel
{
    use HasFactory;
    use UsesUuid;
    use HasStates;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'provider',
        'type',
        'secret',
        'external_id',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'state' => WebhookState::class,
        'secret' => 'encrypted',
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => WebhookCreatedEvent::class,
        'updated' => WebhookUpdatedEvent::class,
        'deleted' => WebhookDeletedEvent::class,
    ];

    /** A service class to handle webhooks related functions. */
    private WebhookServiceInterface $service;

    public function getUserAttribute(): User
    {
        return $this->project->user;
    }

    /** Generate a URL that the external service should be sending payload to. */
    public function getPayloadUrlAttribute(): string
    {
        // This allows to have a separate domain for webhook payloads. Useful mainly for dev/debug purposes.
        if (! empty(config('webhooks.payload_url'))) {
            return config('webhooks.payload_url') .
                route('hook.push', [$this->project->vcsProvider->webhookProvider, $this], false);
        }

        return route('hook.push', [$this->project->vcsProvider->webhookProvider, $this]);
    }

    /** Get the full name (with username) of the repository with this webhook. */
    public function getRepoAttribute(): string
    {
        return $this->project->repo;
    }

    public function isEnabling(): bool
    {
        return $this->state->is(Enabling::class);
    }

    public function isEnabled(): bool
    {
        return $this->state->is(Enabled::class);
    }

    public function isDeleting(): bool
    {
        return $this->state->is(Deleting::class);
    }

    public function service(): WebhookServiceInterface
    {
        // We're caching an instance of WebhookServiceInterface for this model,
        // so it doesn't get made multiple times.
        if (! isset($this->service))
            $this->service = App::make("{$this->provider}-servers");

        return $this->service;
    }

    /** Get a relation with the project that this webhook is attached to. */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** Get a relation with the calls we received for this webhook. */
    public function calls(): HasMany
    {
        return $this->hasMany(WebhookCall::class);
    }
}
