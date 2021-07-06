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
     * Create a new custom Eloquent Collection instance.
     */
    public function newCollection(array $models = []): EloquentCollection
    {
        return new EloquentCollection($models);
    }
}
