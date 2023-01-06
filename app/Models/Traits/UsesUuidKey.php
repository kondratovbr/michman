<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Support\Str;
use Illuminate\Database\Eloquent\Model;

// TODO: Cover with tests.

/**
 * Trait to have UUID as a model attribute and use it for route binding.
 *
 * @property string $uuid
 *
 * @mixin Model
 */
trait UsesUuidKey
{
    /**
     * Boot the trait, adding a creating observer.
     *
     * When persisting a new model instance, we resolve the UUID field,
     * then set a fresh UUID.
     */
    public static function bootUsesUuidKey(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->uuid))
                $model->uuid = Str::uuid()->toString();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
