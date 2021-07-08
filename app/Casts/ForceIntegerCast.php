<?php declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Class ForceIntegerCast
 *
 * Forces null attributes to be casted to (int) 0.
 */
class ForceIntegerCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): int
    {
        return (int) ($value ?? 0);
    }

    public function set($model, string $key, $value, array $attributes): int
    {
        return (int) ($value ?? 0);
    }
}
