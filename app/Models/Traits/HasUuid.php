<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Support\Str;
use Illuminate\Database\Eloquent\Model;

// TODO: Cover with tests.

/**
 * Trait to have UUID as a model attribute.
 *
 * @property string $uuid
 *
 * @mixin Model
 */
trait HasUuid
{
    /**
     * Boot the trait, adding a creating observer.
     *
     * When persisting a new model instance, we resolve the UUID field,
     * then set a fresh UUID.
     */
    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->uuid))
                $model->uuid = Str::uuid()->toString();
        });
    }
}
