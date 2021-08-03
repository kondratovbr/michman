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
 * @property CarbonInterface|null $completedAt
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read Project $project
 * @property-read Collection $servers
 *
 * @method static DeploymentFactory factory(...$parameters)
 */
class Deployment extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'branch',
        'commit',
        'completed_at',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [
        //
    ];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'completed_at' => 'timestamp',
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        //
    ];

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
