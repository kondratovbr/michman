<?php declare(strict_types=1);

namespace App\Support\Traits;

/**
 * Trait ArrayableBasicImmutable
 *
 * Allows to work with an object as with an array by providing full access to the underlying array.
 *
 * Read-only allowed. Cannot be used as to provide access to the associative array. (keys will always be int)
 *
 * @see \ArrayAccess
 */
trait ArrayableBasicImmutable
{
    abstract function toArray(): array;

    private int $position = 0;

    public function offsetExists($offset): bool
    {
        return isset($this->toArray()[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->toArray()[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new \RuntimeException(
            static::class
            . ' is immutable. Tried to change it using array interface (offsetSet).'
            . ' $offset: ' . $offset
            . ', $value: ' . $value
        );
    }

    public function offsetUnset($offset): void
    {
        throw new \RuntimeException(
            static::class
            . ' is immutable. Tried to change it using array interface (offsetUnset).'
            . ' $offset: ' . $offset
        );
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): mixed
    {
        return $this->toArray()[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): string|float|int|bool|null
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->toArray()[$this->position]);
    }

    public function count(): int
    {
        return count($this->toArray());
    }
}
