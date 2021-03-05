<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Support\Str;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait that translates camelCase application-level attributes into snake_case DB-level attributes.
 *
 * @mixin Model
 */
trait UsesCamelCaseAttributes
{
    public function getAttribute($key)
    {
        if (
            array_key_exists($key, $this->relations)
            || method_exists($this, $key)
        ) {
            return parent::getAttribute($key);
        } else {
            return parent::getAttribute(Str::snake($key));
        }
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute(Str::snake($key), $value);
    }

    public function __isset($key): bool
    {
        return parent::__isset($key) || parent::__isset(Str::snake($key));
    }

    public function __unset($key): void
    {
        parent::__unset($key);
        parent::__unset(Str::snake($key));
    }
}
