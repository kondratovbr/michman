<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\IsApiProvider;
use App\Services\ServerProviderInterface;
use Carbon\CarbonInterface;
use Database\Factories\ProviderFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

/**
 * Server Provider Eloquent model
 *
 * Represents an account on a third-party server provider connected to the app over their API,
 * like DigitalOcean or Linode.
 *
 * @property int $id
 * @property string $provider
 * @property string|null $name
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read string $status
 *
 * @property-read User $user
 * @property-read Collection $servers
 *
 * @method static ProviderFactory factory(...$parameters)
 */
class Provider extends AbstractModel
{
    use HasFactory;
    use IsApiProvider;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ERROR = 'error';
    public const STATUS_READY = 'ready';
    public const STATUS_ACTIVE = 'active';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'provider',
        'name',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast. */
    protected $casts = [];

    /** An interface to interact with the API. */
    private ServerProviderInterface $api;

    /** Current status of this provider. */
    private string $status;

    /** Get the current status of this provider. */
    protected function getStatusAttribute(): string
    {
        // We're caching this attribute for an instance,
        // so we don't have to query the DB every time.
        if (isset($this->status))
            return $this->status;

        if (($this->serversCount ?? $this->servers()->count()) > 0)
            return $this->status = static::STATUS_ACTIVE;

        return $this->status = static::STATUS_READY;
    }

    protected function diTargetName(): string
    {
        return "{$this->provider}_servers";
    }

    /**
     * Get an instance of ServerProviderInterface to interact with the server provider API.
     *
     * NOTE: This method must be called outside any DB transactions,
     * because it may need to commit a refreshed access token to the DB.
     *
     * TODO: IMPORTANT! This caveat may be resolved by using a separate DB table for API access tokens and accessing it
     *       over a separate DB connection configured in databases.php config file and the corresponding model.
     */
    public function api(): ServerProviderInterface
    {
        $api = $this->getApi();

        if (! $api instanceof ServerProviderInterface)
            throw new RuntimeException('API instance created for Models/Provider is not an instance of ServerProviderInterface.');

        return $api;
    }

    /** Get a relation to the user who owns this provider account. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Get a relation to the servers created with this provider. */
    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }
}
