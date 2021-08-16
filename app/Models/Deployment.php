<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\DeploymentFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Deployment Eloquent model
 *
 * @property int $id
 * @property string $branch
 * @property string|null $commit
 * @property CarbonInterface|null $startedAt
 * @property CarbonInterface|null $completedAt
 * @property bool|null $successful
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read string $status
 * @property-read CarbonInterface|null $duration
 *
 * @property-read Project $project
 * @property-read Collection $servers
 *
 * @method static DeploymentFactory factory(...$parameters)
 */
class Deployment extends AbstractModel
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_WORKING = 'working';
    public const STATUS_FAILED = 'failed';
    public const STATUS_COMPLETED = 'completed';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'branch',
        'commit',
        'started_at',
        'completed_at',
        'successful',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [
        //
    ];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'started_at' => 'timestamp',
        'completed_at' => 'timestamp',
        'successful' => 'bool',
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        //
    ];

    /**
     * Derive deployment status from its properties.
     */
    public function getStatusAttribute(): string
    {
        if (isset($this->successful))
            return $this->successful ? static::STATUS_COMPLETED : static::STATUS_FAILED;

        return isset($this->startedAt) ? static::STATUS_WORKING : static::STATUS_PENDING;
    }

    /**
     * Calculate how long did it took to fully perform this deployment.
     */
    public function getDurationAttribute(): CarbonInterface|null
    {
        if (! isset($this->completedAt))
            return null;

        return $this->completedAt->sub($this->startedAt);
    }

    /**
     * Get a relation with the project that was deployed by this deployment.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get a relation with the servers where this deployment happened.
     */
    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'deployment_server')
            ->using(DeploymentServerPivot::class)
            ->withTimestamps();
    }
}
