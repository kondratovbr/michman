<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ProviderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Server Eloquent model
 *
 * @property int $id
 * @property string $externalId
 * @property string $name
 * @property string $type
 * @property string $ip
 * @property CarbonInterface $updatedAt
 * @property CarbonInterface $createdAt
 *
 * @property-read Provider $provider
 *
 * @method static ProviderFactory factory(...$parameters)
 */
class Server extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'type',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /**
     * Get a relation to the provider that runs this server.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
