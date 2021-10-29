<?php declare(strict_types=1);

namespace App\Models;

use App\Events\VcsProviders\VcsProviderCreatedEvent;
use App\Events\VcsProviders\VcsProviderDeletedEvent;
use App\Events\VcsProviders\VcsProviderUpdatedEvent;
use App\Services\VcsProviderInterface;
use App\Support\Arr;
use Carbon\CarbonInterface;
use Database\Factories\VcsProviderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

/**
 * VcsProvider Eloquent model
 *
 * Represents an account on a third-party VCS service provider connected to the app over their API,
 * like GitHub or GitLab.
 *
 * @property int $id
 * @property string $provider
 * @property string $externalId
 * @property string $nickname
 * @property string|null $token
 * @property string|null $refreshToken
 * @property CarbonInterface $expiresAt
 * @property string|null $key
 * @property string|null $secret
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read string $webhookProvider
 *
 * @property-read User $user
 *
 * @method static VcsProviderFactory factory(...$parameters)
 */
class VcsProvider extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'provider',
        'external_id',
        'nickname',
        'token',
        'refresh_token',
        'expires_at',
        'key',
        'secret',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'expires_at' => 'datetime',
        'key' => 'encrypted',
        'secret' => 'encrypted',
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => VcsProviderCreatedEvent::class,
        'updated' => VcsProviderUpdatedEvent::class,
        'deleted' => VcsProviderDeletedEvent::class,
    ];

    /** An interface to interact with the API. */
    private VcsProviderInterface $api;

    /** Get an instance of VcsProviderInterface to interact with the VCS provider API. */
    public function api(): VcsProviderInterface
    {
        // We're caching an instance of ServerProviderInterface for this model,
        // so it doesn't get made multiple times.
        if (isset($this->api))
            return $this->api;

        // TODO: CRITICAL! CONTINUE. Figure out refreshing tokens.

        $this->api = App::make(
            "{$this->provider}_vcs",
            isset($this->token)
                ? [
                    'token' => $this->token,
                    'identifier' => $this->id,
                ]
                : [
                    'key' => $this->key,
                    'secret' => $this->secret,
                    'identifier' => $this->id,
                ]
        );

        return isset($this->expiresAt)
            ? $this->ensureFreshToken()
            : $this->api;
    }

    /** Ensure the stored API token is still valid, refresh if needed. */
    protected function ensureFreshToken(): VcsProviderInterface
    {
        return DB::transaction(function (): VcsProviderInterface {
            $model = $this->freshLockForUpdate();

            if ($this->expiresAt->greaterThan(now()))
                return $this->api;

            $this->fill($this->api->refreshToken($this->refreshToken)->toAttributes());
            $this->save();

            // Remove the existing API object to reconstruct it with the new token.
            unset($this->api);

            return $this->api();
        }, 5);
    }

    /** Get the name of the webhook provider corresponding to this VCS provider. */
    public function getWebhookProviderAttribute(): string
    {
        return Arr::firstKey(
            Arr::filterAssoc(config('webhooks.providers'),
                fn(string $hookProvider, array $config) => $config['vcs_provider'] === $this->provider
            )
        );
    }

    /** Get a relation with the user that owns this VCS provider account. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Get a relation with the projects that use repositories by this VCS provider. */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
