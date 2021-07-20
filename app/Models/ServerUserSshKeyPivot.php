<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;

/**
 * Pivot model for Server to UserSshKey relation
 *
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 */
class ServerUserSshKeyPivot extends AbstractPivot
{
    //
}
