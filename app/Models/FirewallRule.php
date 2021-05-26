<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\FirewallRuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * FirewallRule Eloquent model
 *
 * @property int $id
 * @property string $name
 * @property string $port
 * @property string $fromIp
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read Server $server
 *
 * @method static FirewallRuleFactory factory(...$parameters)
 */
class FirewallRule extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'port',
        'from_ip',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /**
     * Get a relation to the server that has this rule.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
