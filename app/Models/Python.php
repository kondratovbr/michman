<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\PythonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Python Eloquent model
 *
 * @property int $id
 * @property string $version
 * @property string|null $status
 * @property string|null $patchVersion
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 *
 * @property-read Server $server
 *
 * @method static PythonFactory factory(...$parameters)
 */
class Python extends AbstractModel
{
    use HasFactory;

    public const STATUS_INSTALLED = 'installed';
    public const STATUS_INSTALLING = 'installing';
    public const STATUS_UPDATING = 'updating';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'version',
        'status',
        'patch_version',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    public function getStatusAttribute(): string
    {
        return $this->attributes['status'] ?? static::STATUS_INSTALLING;
    }

    public function getUserAttribute(): User
    {
        return $this->server->provider->owner;
    }

    /**
     * Check if this instance of Python was installed on the server.
     */
    public function isInstalled(): bool
    {
        return in_array($this->status, [
            static::STATUS_INSTALLED,
            static::STATUS_UPDATING,
        ]);
    }

    /**
     * Check if this instance of Python is currently updating.
     */
    public function isUpdating(): bool
    {
        return $this->status === static::STATUS_UPDATING;
    }

    /**
     * Get a relation with the server where this Python instance is installed.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
