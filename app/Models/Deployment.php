<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Deployments\DeploymentCreatedEvent;
use App\Events\Deployments\DeploymentUpdatedEvent;
use App\QueryBuilders\DeploymentQueryBuilder;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Database\Factories\DeploymentFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// TODO: Later I will need to somehow distinguish manual deployments from automatic ones.

/**
 * Deployment Eloquent model
 *
 * Represents a single complete deployment that can happen at multiple servers,
 * DeploymentServerPivot contains information about that process on a single server.
 *
 * @property int $id
 * @property string $branch
 * @property string|null $commit
 * @property string|null $environment
 * @property string|null $deployScript
 * @property string|null $gunicornConfig
 * @property string|null $nginxConfig
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read bool $started
 * @property-read bool $finished
 * @property-read bool|null $successful
 * @property-read bool|null $failed
 * @property-read string $status
 * @property-read CarbonInterface|null $startedAt
 * @property-read CarbonInterface|null $finishedAt
 * @property-read CarbonInterval|null $duration
 * @property-read string $createdAtFormatted
 *
 * @property-read User $user
 * @property-read Project $project
 * @property-read Collection $servers
 * @property-read DeploymentServerPivot|null $serverDeployment
 *
 * @method static DeploymentQueryBuilder query()
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
        'environment',
        'deploy_script',
        'gunicorn_config',
        'nginx_config',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [
        //
    ];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        //
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => DeploymentCreatedEvent::class,
        'updated' => DeploymentUpdatedEvent::class,
    ];

    /**
     * Get the user who owns the project that is being deployed by this deployment.
     */
    public function getUserAttribute(): User
    {
        /*
         * TODO: IMPORTANT! I need to re-do the Deployment in such a way that it can be started by a different user
         *       and, obviously, ensure that this user is allowed to do so,
         *       since a project may be managed by a team.
         */
        return $this->project->user;
    }

    /**
     * Check if this deployment has been started.
     */
    public function getStartedAttribute(): bool
    {
        return $this->servers->reduce(
            fn(bool $started, Server $server) => $started ? true : $server->serverDeployment->started,
            false
        );
    }

    /**
     * Check if this deployment is finished (regardless of its success) or is it still going.
     */
    public function getFinishedAttribute(): bool
    {
        return $this->servers->reduce(
            fn(bool $finished, Server $server) => $finished ? $server->serverDeployment->finished : $finished,
            true
        );
    }

    /**
     * Check if this deployment was successful.
     */
    public function getSuccessfulAttribute(): bool|null
    {
        /** @var Server $server */
        foreach ($this->servers as $server) {
            if (! $server->serverDeployment->finished)
                return null;

            if ($server->serverDeployment->failed)
                return false;
        }

        return true;
    }

    /**
     * Check if the deployment has failed.
     */
    public function getFailedAttribute(): bool|null
    {
        if (is_null($this->successful))
            return null;

        return ! $this->successful;
    }

    /**
     * Derive the status of this deployment from the properties of its pivots with servers.
     */
    public function getStatusAttribute(): string
    {
        if (! $this->started)
            return static::STATUS_PENDING;

        if (! $this->finished)
            return static::STATUS_WORKING;

        return $this->successful ? static::STATUS_COMPLETED : static::STATUS_FAILED;
    }

    /**
     * Get a timestamp when this deployment was started on any server.
     */
    public function getStartedAtAttribute(): CarbonInterface|null
    {
        return $this->servers->pluck('serverDeployment')->min('started_at');
    }

    /**
     * Get a timestamp when this deployment was finished on the last server it happened on.
     */
    public function getFinishedAtAttribute(): CarbonInterface|null
    {
        return $this->servers->pluck('serverDeployment')->max('finished_at');
    }

    /**
     * Calculate the duration of this deployment as the longest time any of
     * the server spent working on it.
     */
    public function getDurationAttribute(): CarbonInterval|null
    {
        if (! $this->finished)
            return null;

        return $this->finishedAt->diffAsCarbonInterval($this->startedAt);
    }

    /** Get the $createdAt attribute nicely formatted for the UI. */
    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->createdAt->diffAsCarbonInterval(now())->lessThan(CarbonInterval::day())
            ? $this->createdAt->diffForHumans()
            : $this->createdAt->toDayDateTimeString();
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
            ->as(DeploymentServerPivot::ACCESSOR)
            ->using(DeploymentServerPivot::class)
            ->withPivot(DeploymentServerPivot::$pivotAttributes)
            ->withTimestamps();
    }

    public function newEloquentBuilder($query): DeploymentQueryBuilder
    {
        return new DeploymentQueryBuilder($query);
    }
}
