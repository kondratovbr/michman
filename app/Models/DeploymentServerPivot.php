<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;

/**
 * Pivot model for Deployment to Server relation
 *
 * Represents the deployment process performed on a specific server.
 *
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
    /** @var string[] The attributes that aren't mass assignable. */
    protected $guarded = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'started_at' => 'timestamp',
        'finished_at' => 'timestamp',
        'successful' => 'bool',
    ];

    /**
     * Check if the deployment on the server has started.
     */
    public function getStartedAttributes(): bool
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
}
