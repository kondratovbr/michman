<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;

/**
 * Pivot model for ServerSshKey to VcsProvider deployment.
 *
 * Represents a key added to a VCS provider account.
 *
 * @property int $serverSshKeyId
 * @property int $vcsProviderId
 * @property string|null $externalId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 */
class ServerSshKeyVcsProviderPivot extends AbstractPivot
{
    /** @var string Custom pivot accessor name. */
    public const ACCESSOR = 'vcsProviderKey';

    /**
     * Additional attribute names that this pivot model has.
     * These attributes will be retrieved for the pivot each time.
     *
     * @var string[]
     */
    public static array $pivotAttributes = [
        'external_id',
    ];

    /** @var string[] The attributes that aren't mass assignable. */
    protected $guarded = [];
}
