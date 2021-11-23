<?php declare(strict_types=1);

namespace App\Models;

use App\Collections\EloquentCollection;
use App\Models\Traits\HasModelHelpers;
use App\Models\Traits\IsLockable;
use App\Models\Traits\UsesCamelCaseAttributes;
use App\Validation\Rules;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

// TODO: CRITICAL! Cover with tests as well.

/**
 * Customized base model class for Eloquent models
 */
abstract class AbstractModel extends Model
{
    use UsesCamelCaseAttributes;
    use HasModelHelpers;
    use IsLockable;

    /**
     * Check that model key is inside the collection provided
     * and that model still exists in the DB.
     * Retrieve the model from the DB and return it.
     */
    public static function validated(string|int $key, Collection $models, bool $strictUuid = false): static
    {
        if (! $models->every(fn($item) => $item instanceof static))
            throw new RuntimeException('The provided Collection contains items that aren\'t of this model class (' . static::class . ')');

        $key = Validator::make(
            ['key' => (string) $key],
            [
                'key' => $strictUuid
                    ? Rules::uuid()->in($models->modelKeys())->required()
                    : Rules::string(1, 64)->in($models->modelKeys())->required()
            ],
        )->validate()['key'];

        /** @var static $model */
        $model = static::query()->findOrFail($key);

        return $model;
    }

    /** Create a new custom Eloquent Collection instance. */
    public function newCollection(array $models = []): EloquentCollection
    {
        return new EloquentCollection($models);
    }
}
