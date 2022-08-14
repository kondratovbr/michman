<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;

/**
 * Pivot model for Project to Server relation
 *
 * @property int $id
 *
 * IDs
 * @property int $projectId
 * @property int $serverId
 *
 * Properties
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @mixin IdeHelperProjectServerPivot
 */
class ProjectServerPivot extends AbstractPivot
{
    /**
     * Additional attribute names that this pivot model has.
     * These attributes will be retrieved for the pivot each time.
     *
     * @var string[]
     */
    public static array $pivotAttributes = [
        //
    ];
}
