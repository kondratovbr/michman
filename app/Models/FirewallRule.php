<?php declare(strict_types=1);

namespace App\Models;

use App\Casts\ForceBooleanCast;
use App\Events\Firewall\FirewallRuleCreatedEvent;
use App\Events\Firewall\FirewallRuleDeletedEvent;
use App\Events\Firewall\FirewallRuleUpdatedEvent;
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
 * @property bool $canDelete
 * @property string $status
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 *
 * @property-read Server $server
 *
 * @method static FirewallRuleFactory factory(...$parameters)
 */
class FirewallRule extends AbstractModel
{
    use HasFactory;

    public const STATUS_ADDED = 'added';
    public const STATUS_ADDING = 'adding';
    public const STATUS_DELETING = 'deleting';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'port',
        'from_ip',
        'can_delete',
        'status',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes castings. */
    protected $casts = [
        'can_delete' => ForceBooleanCast::class,
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => FirewallRuleCreatedEvent::class,
        'updated' => FirewallRuleUpdatedEvent::class,
        'deleted' => FirewallRuleDeletedEvent::class,
    ];

    /**
     * Get the current status of this firewall rule.
     */
    public function getStatusAttribute(): string
    {
        return $this->attributes['status'] ?? static::STATUS_ADDING;
    }

    /**
     * Get the owner of the server where this firewall rule belongs.
     */
    public function getUserAttribute(): User
    {
        return $this->server->provider->owner;
    }

    /**
     * Check if the rule was added to the server.
     */
    public function isAdded(): bool
    {
        return $this->status === static::STATUS_ADDED;
    }

    /**
     * Check if the rule is in the process of being added to the server.
     */
    public function isAdding(): bool
    {
        return $this->status === static::STATUS_ADDING;
    }

    /**
     * Check if the rule is in the process of being deleted from the server.
     */
    public function isDeleting(): bool
    {
        return $this->status === static::STATUS_DELETING;
    }

    /**
     * Get a relation to the server that has this rule.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
