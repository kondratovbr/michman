<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;

/*
 * TODO: CRITICAL! Cover with tests. I should really cover models with tests as well. I have lots of data access logic in them.
 */

/**
 * Pivot model for Deployment to Server relation
 *
 * Represents the deployment process performed on a specific server.
 *
 * @property int $deploymentId
 * @property int $serverId
 * @property CarbonInterface|null $startedAt
 * @property CarbonInterface|null $finishedAt
 * @property bool|null $successful
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read bool $started
 * @property-read bool $finished
 * @property-read CarbonInterval|null $duration
 */
class DeploymentServerPivot extends AbstractPivot
{
    /**
     * Additional attribute names that this pivot model has.
     * These attributes will be retrieved for the pivot each time.
     *
     * @var array
     */
    public static array $pivotAttributes = [
        'started_at',
        'finished_at',
        'successful',
    ];

    /** @var string[] The attributes that aren't mass assignable. */
    protected $guarded = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'successful' => 'bool',
    ];

    /**
     * Check if the deployment on the server has started.
     */
    public function getStartedAttribute(): bool
    {
        return isset($this->startedAt);
    }

    /**
     * Check if the deployment on the server is finished (regardless if its success) or is it still in progress.
     */
    public function getFinishedAttribute(): bool
    {
        return isset($this->finishedAt);
    }

    /**
     * Calculate the time the server spent working on the deployment.
     */
    public function getDurationAttribute(): CarbonInterval|null
    {
        if (is_null($this->startedAt) || is_null($this->finishedAt))
            return null;

        return $this->finishedAt->diffAsCarbonInterval($this->startedAt);
    }

    /**
     * Check if the deployment on the server has failed.
     */
    public function getFailedAttribute(): bool|null
    {
        if (is_null($this->successful))
            return null;

        return ! $this->successful;
    }

    /**
     * Retrieve the logs of the deployment on the server.
     */
    public function logs(): Collection
    {
        return ServerLog::query()
            ->where('server_id', $this->serverId)
            ->whereBetween('created_at', [$this->startedAt, $this->finishedAt])
            ->oldest()
            ->get();
    }
}
