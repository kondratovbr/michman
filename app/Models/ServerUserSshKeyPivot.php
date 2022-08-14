<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;

/**
 * Pivot model for Server to UserSshKey relation
 *
 * @property int $id
 *
 * IDs
 * @property int $userId
 * @property int $serverId
 *
 * Properties
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @mixin IdeHelperServerUserSshKeyPivot
 */
class ServerUserSshKeyPivot extends AbstractPivot
{
    /** @var string[] The attributes that aren't mass assignable. */
    protected $guarded = [];

    //
}
