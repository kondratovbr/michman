<?php declare(strict_types=1);

namespace App\Casts;

use App\DataTransferObjects\AbstractDto;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

// TODO: CRITICAL! CONTINUE. And cover this with tests as soon as it is ready.

class DtoCast implements CastsAttributes
{
    public function __construct(
        protected string $dtoClass,
    ) {}

    /**
     * Cast the given value.
     *
     * @param Model $model
     */
    public function get($model, string $key, $value, array $attributes): AbstractDto
    {
        if (! is_string($value))
            throw new RuntimeException('Value received for DtoCast::get() should be a string.');

        return $this->dtoClass::fromArray(json_decode($value, true));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param AbstractDto|array $value
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        if (is_array($value))
            $value = $this->dtoClass::fromArray($value);

        if (! $value instanceof AbstractDto)
            throw new RuntimeException('Value provided for DtoCast::set() should be either an instance of AbstractDto or an array to be used to create such instance.');

        return json_encode($value->toArray());
    }
}
