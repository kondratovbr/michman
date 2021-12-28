<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\AbstractModel;
use App\Support\Str;

// TODO: IMPORTANT! Cover with tests!

/**
 * Trait to have UUID as a model attribute and use it for route binding.
 *
 * @property string $uuid
 *
 * @mixin AbstractModel
 */
trait UsesUuid
{
    /**
     * Boot the trait, adding a creating observer.
     *
     * When persisting a new model instance, we resolve the UUID field,
     * then set a fresh UUID.
     */
    public static function bootUsesUuid(): void
    {
        static::creating(function (AbstractModel $model) {
            if (empty($model->uuid))
                $model->uuid = Str::uuid()->toString();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
