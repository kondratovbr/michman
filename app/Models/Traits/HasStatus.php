<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\AbstractModel;
use App\Support\Arr;

/**
 * Trait to add a $status property to Eloquent models.
 *
 * @property string $status
 *
 * @mixin AbstractModel
 */
trait HasStatus
{
    /**
     * Check if this model has one of the statuses provided.
     */
    public function isStatus(string|array $statuses): bool
    {
        return Arr::hasValue(Arr::wrap($statuses), $this->status);
    }
}
