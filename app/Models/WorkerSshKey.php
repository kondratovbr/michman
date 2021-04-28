<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\WorkerSshKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WorkerSshKey Eloquent model
 *
 * Represents an SSH key that our worker uses to access the server.
 *
 * @property int $id
 * @property string $publicKey
 * @property string $privateKey
 * @property string $name
 * @property string|null $externalId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @method static WorkerSshKeyFactory factory(...$parameters)
 */
class WorkerSshKey extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'public_key',
        'private_key',
        'name',
        'external_id',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast. */
    protected $casts = [
        'private_key' => 'encrypted',
    ];

    /**
     * Get a relation with the server that uses this key.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
