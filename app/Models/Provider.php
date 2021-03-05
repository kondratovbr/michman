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
 * @property string $name
 * @property string $api_key
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

    /**
     * Get a relation to the user who owns this provider account.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
