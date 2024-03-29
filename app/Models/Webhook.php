<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Webhooks\WebhookCreatedEvent;
use App\Events\Webhooks\WebhookDeletedEvent;
use App\Events\Webhooks\WebhookUpdatedEvent;
use App\Models\Traits\UsesUuidKey;
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
 *
 * IDs
 * @property int $projectId
 *
 * Properties
 * @property string $uuid
 * @property string $provider
 * @property string $type
 * @property string $url
 * @property string $secret
 * @property string|null $externalId
 * @property WebhookState $state
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * Relations
 * @property-read User $user
 * @property-read string $repo
 * @property-read Project $project
 * @property-read Collection $calls
 *
 * @method static WebhookFactory factory(...$parameters)
 *
 * @mixin IdeHelperWebhook
 */
class Webhook extends AbstractModel
{
    use HasFactory;
    use UsesUuidKey;
    use HasStates;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'provider',
        'type',
        'url',
        'secret',
        'external_id',
        'state',
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

    public static function booted(): void
    {
        static::creating(function (Webhook $hook) {
            if (empty($hook->url))
                $hook->url = static::payloadUrl($hook);
        });
    }

    protected function getUserAttribute(): User
    {
        return $this->project->user;
    }

    /** Get the full name (with username) of the repository with this webhook. */
    protected function getRepoAttribute(): string
    {
        return $this->project->repo;
    }

    /** Generate a payload URL for a webhook. */
    public static function payloadUrl(Webhook $hook): string
    {
        // This allows to have a separate domain for webhook payloads. Useful mainly for dev/debug purposes.
        if (! empty(config('webhooks.payload_url'))) {
            return config('webhooks.payload_url') .
                route('hook.push', [$hook->provider, $hook], false);
        }

        return route('hook.push', [$hook->provider, $hook]);
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
            $this->service = App::make("{$this->provider}_webhooks");

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

    public function purge(): bool|null
    {
        $this->calls->each->purge();

        return $this->delete();
    }
}
