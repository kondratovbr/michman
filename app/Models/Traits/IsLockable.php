<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Support\Arr;
use Illuminate\Database\Eloquent\Model;

// TODO: CRITICAL! Cover with tests as well.

/**
 * Trait with methods for locking models in the database.
 *
 * @mixin Model
 */
trait IsLockable
{
    /** Retrieve a new instance of this model from the database and apply an UPDATE LOCK on it. */
    public function freshLockForUpdate(array|string $with = []): static
    {
        $query = $this->newQuery();

        if (! empty($with))
            $query->with(Arr::wrap($with));

        /** @var static $model */
        $model = $query->lockForUpdate()->findOrFail($this->getKey());

        return $model;
    }

    /** Retrieve a new instance of this model from the database and apply a SHARED LOCK on it. */
    public function freshSharedLock(array|string $with = []): static
    {
        $query = $this->newQuery();

        if (! empty($with))
            $query->with(Arr::wrap($with));

        /** @var static $model */
        $model = $query->sharedLock()->findOrFail($this->getKey());

        return $model;
    }
}
