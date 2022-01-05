<?php declare(strict_types=1);

namespace App\Casts;

use Ds\Set;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use RuntimeException;

class SetCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): Set
    {
        return new Set(json_decode($value));
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        if (is_array($value))
            $value = new Set($value);

        if (! $value instanceof Set)
            throw new RuntimeException('Instance of Ds/Set or an array expected.');

        return json_encode($value);
    }
}
