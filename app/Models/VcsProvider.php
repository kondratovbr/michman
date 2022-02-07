<?php declare(strict_types=1);

namespace App\Models;

use App\Events\VcsProviders\VcsProviderCreatedEvent;
use App\Events\VcsProviders\VcsProviderDeletedEvent;
use App\Events\VcsProviders\VcsProviderUpdatedEvent;
use App\Models\Traits\IsApiProvider;
use App\Services\VcsProviderInterface;
use App\Support\Arr;
use Carbon\CarbonInterface;
use Database\Factories\VcsProviderFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

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
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read string $webhookProvider
 *
 * @property-read User $user
 * @property-read Collection $projects
 * @property-read ServerSshKeyVcsProviderPivot|null $vcsProviderKey
 * @property-read OAuthUser|null $oauthUser
 *
 * @method static VcsProviderFactory factory(...$parameters)
 */
class VcsProvider extends AbstractModel
{
    use HasFactory;
    use IsApiProvider;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'provider',
        'external_id',
        'nickname',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => VcsProviderCreatedEvent::class,
        'updated' => VcsProviderUpdatedEvent::class,
        'deleted' => VcsProviderDeletedEvent::class,
    ];

    protected function diTargetName(): string
    {
        return "{$this->provider}_vcs";
    }

    /** Get an instance of VcsProviderInterface to interact with the VCS provider API. */
    public function api(): VcsProviderInterface
    {
        $api = $this->getApi();

        if (! $api instanceof VcsProviderInterface)
            throw new RuntimeException('API instance created for Models/VcsProvider is not an instance of VcsProviderInterface.');

        return $api;
    }

    /** Get the name of the webhook provider corresponding to this VCS provider. */
    protected function getWebhookProviderAttribute(): string
    {
        return Arr::firstKey(
            Arr::filterAssoc(config('webhooks.providers'),
                fn(string $hookProvider, array $config) => $config['vcs_provider'] === $this->provider
            )
        );
    }

    /**
     * Check if this VCS provider doesn't support server keys,
     * i.e. we can't add an SSH key to the user's account.
     */
    public function mustUseDeployKey(): bool
    {
        return ! (bool) config("vcs.list.{$this->provider}.supports_ssh_keys", false);
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

    /** Get the relation with the ServerSshKeys that were added to this provider. */
    public function serverSshKeys(): BelongsToMany
    {
        return $this->belongsToMany(ServerSshKey::class, 'server_ssh_key_vcs_provider')
            ->as(ServerSshKeyVcsProviderPivot::ACCESSOR)
            ->using(ServerSshKeyVcsProviderPivot::class)
            ->withPivot(ServerSshKeyVcsProviderPivot::$pivotAttributes)
            ->withTimestamps();
    }

    /** Get a relation with the corresponding OAuthUser, if any. */
    public function oauthUser(): BelongsTo
    {
        return $this->belongsTo(OAuthUser::class, 'oauth_user_id');
    }
}
