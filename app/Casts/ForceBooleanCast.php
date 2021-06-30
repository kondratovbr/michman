<?php declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * ForceBooleanCast for Eloquent models
 *
 * Forces null attributes to be casted to false,
 * unlike the built-in Laravel "boolean" cast.
 */
class ForceBooleanCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): bool
    {
        return (bool) $value;
    }

    public function set($model, string $key, $value, array $attributes): bool
    {
        return (bool) $value;
    }
}
