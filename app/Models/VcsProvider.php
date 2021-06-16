<?php declare(strict_types=1);

namespace App\Models;

use App\Services\VcsProviderInterface;
use Carbon\CarbonInterface;
use Database\Factories\VcsProviderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

/**
 * VcsProvider Eloquent model
 *
 * Represents an account on a third-party VCS service provider connected to the app over their API,
 * like GitHub or GitLab.
 *
 * @property int $id
 * @property string $provider
 * @property string|null $token
 * @property string|null $key
 * @property string|null $secret
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
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
        'token',
        'key',
        'secret',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'token' => 'encrypted',
        'key' => 'encrypted',
        'secret' => 'encrypted',
    ];

    /** @var VcsProviderInterface An interface to interact with the API. */
    private VcsProviderInterface $api;

    /**
     * Get an instance of VcsProviderInterface to interact with the VCS provider API.
     */
    public function api(): VcsProviderInterface
    {
        // We're caching an instance of ServerProviderInterface for this model,
        // so it doesn't get made multiple times.
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
     * Get a relation with the user that owns this VCS provider account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
