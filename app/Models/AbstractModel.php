<?php declare(strict_types=1);

namespace App\Models;

use App\Collections\EloquentCollection;
use App\Models\Traits\HasModelHelpers;
use App\Models\Traits\UsesCamelCaseAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Customized base model class for Eloquent models
 */
abstract class AbstractModel extends Model
{
    use UsesCamelCaseAttributes,
        HasModelHelpers;

    /**
     * Retrieve a new instance of this model from the database and apply an UPDATE LOCK on it.
     *
     * @return static
     */
    public function freshLockForUpdate(): static
    {
        // TODO: Do I even use this? And does it even work as it should?

        return $this->newQuery()->lockForUpdate()->findOrFail($this->getKey());
    }

    /**
     * Create a new custom Eloquent Collection instance.
     */
    public function newCollection(array $models = []): EloquentCollection
    {
        return new EloquentCollection($models);
    }
}
