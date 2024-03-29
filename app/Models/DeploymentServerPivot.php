<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;

/*
 * TODO: IMPORTANT! Cover with tests. I should really cover models with tests as well. I have lots of data access logic in them.
 */

/**
 * Pivot model for Deployment to Server relation
 *
 * Represents the deployment process performed on a specific server.
 *
 * @property int $id
 *
 * IDs
 * @property int $deploymentId
 * @property int $serverId
 *
 * Properties
 * @property CarbonInterface|null $startedAt
 * @property CarbonInterface|null $finishedAt
 * @property bool|null $successful
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * Custom attributes
 * @property-read bool $started
 * @property-read bool $finished
 * @property-read CarbonInterval|null $duration
 *
 * @mixin IdeHelperDeploymentServerPivot
 */
class DeploymentServerPivot extends AbstractPivot
{
    /** @var string Custom pivot accessor name. */
    public const ACCESSOR = 'serverDeployment';

    /**
     * Additional attribute names that this pivot model has.
     * These attributes will be retrieved for the pivot each time.
     *
     * @var string[]
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

    /** Check if the deployment on the server has started. */
    protected function getStartedAttribute(): bool
    {
        return isset($this->startedAt);
    }

    /** Check if the deployment on the server is finished (regardless if its success) or is it still in progress. */
    protected function getFinishedAttribute(): bool
    {
        return isset($this->finishedAt);
    }

    /** Calculate the time the server spent working on the deployment. */
    protected function getDurationAttribute(): CarbonInterval|null
    {
        if (is_null($this->startedAt) || is_null($this->finishedAt))
            return null;

        return $this->finishedAt->diffAsCarbonInterval($this->startedAt);
    }

    /** Check if the deployment on the server has failed. */
    protected function getFailedAttribute(): bool|null
    {
        if (is_null($this->successful))
            return null;

        return ! $this->successful;
    }

    /** Retrieve the logs of the deployment on the server. */
    public function logs(): Collection
    {
        return ServerLog::query()
            ->where('server_id', $this->serverId)
            ->whereBetween('created_at', [$this->startedAt, $this->finishedAt])
            ->oldest()
            ->get();
    }
}
