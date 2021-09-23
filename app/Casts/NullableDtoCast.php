<?php declare(strict_types=1);

namespace App\Casts;

use App\DataTransferObjects\AbstractDto;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class NullableDtoCast implements CastsAttributes
{
    public function __construct(
        protected string $dtoClass,
    ) {}

    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string|null $value
     */
    public function get($model, string $key, $value, array $attributes): AbstractDto|null
    {
        if (is_null($value))
            return null;

        if (! is_string($value))
            throw new RuntimeException('Value received for NullableDtoCast::get() should be either a string or null.');

        return $this->dtoClass::fromArray(json_decode($value, true));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param AbstractDto|array|null $value
     */
    public function set($model, string $key, $value, array $attributes): string|null
    {
        if (is_null($value))
            return null;

        if (is_array($value))
            $value = $this->dtoClass::fromArray($value);

        if (! $value instanceof AbstractDto)
            throw new RuntimeException('Value provided for NullableDtoCast::set() should be either an instance of AbstractDto, or an array or null.');

        return json_encode($value->toArray());
    }
}
