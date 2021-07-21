<?php declare(strict_types=1);

namespace App\Casts;

use App\Support\Str;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Lowercase String Model Attribute Cast
 *
 * Turns strings to lowercase.
 */
class Lowercase implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): string|null
    {
        if (is_null($value))
            return null;

        return Str::lower($value);
    }

    public function set($model, string $key, $value, array $attributes): string|null
    {
        if (is_null($value))
            return null;

        return Str::lower($value);
    }
}
