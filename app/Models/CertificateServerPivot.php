<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;

/**
 * Pivot model for Certificate to Server relation
 *
 * Represents an SSL certificate being installed on a specific server.
 *
 * @property int $certificateId
 * @property int $serverId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 */
class CertificateServerPivot extends AbstractPivot
{
    /** @var string Custom pivot accessor name. */
    public const ACCESSOR = 'certificateInstallation';

    /**
     * Additional attribute names that this pivot model has.
     * These attributes will be retrieved for the pivot each time.
     *
     * @var string[]
     */
    public static array $pivotAttributes = [
        //
    ];

    /** @var string[] The attributes that aren't mass assignable. */
    protected $guarded = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        //
    ];
}
