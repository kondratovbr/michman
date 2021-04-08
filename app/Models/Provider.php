<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ProviderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get a relation to the user who owns this provider account.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
