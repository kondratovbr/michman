<?php declare(strict_types=1);

namespace App\Models;

use App\Services\ServerProviderInterface;
use Carbon\CarbonInterface;
use Database\Factories\ProviderFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;

/**
 * Server Provider Eloquent model
 *
 * Represents a server provider connected to the app over their API,
 * like DigitalOcean or Linode.
 *
 * @property int $id
 * @property string $provider
 * @property string|null $name
 * @property string|null $token
 * @property string|null $key
 * @property string|null $secret
 * @property CarbonInterface $created_at
 * @property CarbonInterface $updated_at
 *
 * @property-read User $owner
 * @property-read Collection $servers
 *
 * @method static ProviderFactory factory(...$parameters)
 */
class Provider extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'provider',
        'token',
        'key',
        'secret',
        'name',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var ServerProviderInterface An interface to interact with the API. */
    private ServerProviderInterface $api;

    /**
     * Get an instance of ServerProviderInterface to interact with the server provider API.
     */
    public function api(): ServerProviderInterface
    {
        if (! isset($this->api)) {
            $this->api = App::make(
                $this->provider,
                isset($this->token)
                    ? ['token' => $this->token, 'identifier' => $this->id]
                    : ['key' => $this->key, 'secret' => $this->secret, 'identifier' => $this->id]
            );
        }

        return $this->api;
    }

    /**
     * Get a relation to the user who owns this provider account.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get a relation to the servers created with this provider.
     */
    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }
}
