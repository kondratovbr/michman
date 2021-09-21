<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Casts\DtoCast;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Arrayable;
use App\Support\Arr;
use ReflectionClass;
use ReflectionProperty;

abstract class AbstractDto implements Arrayable, Castable
{
    protected array $exceptKeys = [];

    protected array $onlyKeys = [];

    public static function fromArray(array $data): static
    {
        $class = new ReflectionClass(static::class);

        $properties = Arr::filter(
            $class->getProperties(ReflectionProperty::IS_PUBLIC),
            fn(ReflectionProperty $property) => ! $property->isStatic(),
        );

        $names = Arr::map($properties, fn(ReflectionProperty $property) => $property->getName());

        $data = Arr::filterAssoc($data, fn($key, $value) => Arr::hasValue($names, $key));

        return new static(...$data);
    }

    public function toArray(array $add = []): array
    {
        if (count($this->onlyKeys)) {
            $array = Arr::only($this->all(), $this->onlyKeys);
        } else {
            $array = Arr::except($this->all(), $this->exceptKeys);
        }

        $array = $this->parseArray($array);

        return Arr::merge($array, $add);
    }

    public function only(string ...$keys): static
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->onlyKeys = [...$this->onlyKeys, ...$keys];

        return $dataTransferObject;
    }

    public function except(string ...$keys): static
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->exceptKeys = [...$this->exceptKeys, ...$keys];

        return $dataTransferObject;
    }

    protected function all(): array
    {
        $class = new ReflectionClass(static::class);

        $properties = Arr::filter(
            $class->getProperties(ReflectionProperty::IS_PUBLIC),
            fn(ReflectionProperty $property) => ! $property->isStatic(),
        );

        return Arr::mapAssoc($properties, fn(int $index, ReflectionProperty $property) =>
            [$property->getName(), $property->getValue($this)]
        );
    }

    protected function parseArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if ($value instanceof AbstractDto) {
                $array[$key] = $value->toArray();

                continue;
            }

            if (! is_array($value)) {
                continue;
            }

            $array[$key] = $this->parseArray($value);
        }

        return $array;
    }

    /** Get the caster object to use when casting from/to a DTO. */
    public static function castUsing(array $arguments): DtoCast
    {
        return new DtoCast(static::class);
    }
}
