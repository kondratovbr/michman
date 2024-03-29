<?php declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Arr as IlluminateArr;

/**
 * Custom support class for manipulating arrays
 */
class Arr extends IlluminateArr
{
    /**
     * Check if the array has the key specified.
     */
    public static function hasKey(array $haystack, $needle): bool
    {
        return static::has($haystack, $needle);
    }

    /**
     * Check if the array has the value specified.
     *
     * Wrapper for the built-in in_array() function.
     *
     * @param bool $strict // Use strict comparisons, i.e. compare types as well, do not typecast.
     */
    public static function hasValue(array $haystack, $needle, bool $strict = null): bool
    {
        return in_array($needle, $haystack, (bool) $strict);
    }

    /**
     * Apply the callback to the elements of the given array, return the array of results.
     *
     * Does not modify the source array.
     * Wrapper for the built-in array_map() function.
     */
    public static function map(array $array, callable $callback): array
    {
        return array_map($callback, $array);
    }

    /**
     * Map an array using its keys as well as values.
     *
     * Callback must receive a key as its first parameter and a value as a second one, i.e. like
     *   function($key, $value) {...}
     *
     * Callback must return a single value.
     *
     * @param bool $keepKeys Keep original keys. Will be replaced by the default integer keys otherwise.
     */
    public static function mapWithKeys(array $array, \Closure $callback, bool $keepKeys = false): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $element = $callback($key, $value);
            if ($keepKeys)
                $result[$key] = $element;
            else
                $result[] = $element;
        }

        return $result;
    }

    /**
     * Reduce the array to a single value using the callback and the initial value provided.
     *
     * Wrapper for the built-in array_reduce() function.
     */
    public static function reduce(array $array, \Closure $callback, $initial = null)
    {
        return array_reduce($array, $callback, $initial);
    }

    /**
     * Filter the array using the callback provided.
     *
     * Callback must receive a single element - array item.
     *
     * Callback must return true to keep a corresponding element in the new array or false otherwise.
     *
     * Array keys are preserved.
     */
    public static function filter(array $array, \Closure $callback = null): array
    {
        return array_filter($array, $callback, 0);
    }

    /**
     * Filter an associative array using the given callback. Can use keys as well as values.
     *
     * Callback must receive a key as its first parameter and a value as a second one, i.e. like
     *   function($key, $value) {...}
     *
     * Callback must return true to keep a corresponding element in the new array or false otherwise.
     */
    public static function filterAssoc(array $array, \Closure $callback): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if ($callback($key, $value))
                $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Map an array using its keys.
     *
     * @param bool $keepKeys Keep original keys. Will be replaced by the default integer keys otherwise.
     */
    public static function mapKeys(array $array, \Closure $callback, bool $keepKeys = false): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $element = $callback($key);
            if ($keepKeys)
                $result[$key] = $element;
            else
                $result[] = $element;
        }

        return $result;
    }

    /**
     * Map an associative array using the given callback to a new associative array. Can map keys as well as values.
     *
     * Callback must receive a key as its first parameter and a value as a second one, i.e. like
     *   function($key, $value) {...}
     *
     * Callback must return an instance of \Dr\Pair or an array like [$key, $value].
     */
    public static function mapAssoc(array $array, \Closure $callback): array
    {
        $result = [];

        foreach ($array as $key => $item) {

            $element = $callback($key, $item);

            if ($element instanceof \Ds\Pair)
                $result[$element->key] = $element->value;
            elseif(is_array($element) && count($element) == 2)
                $result[$element[0]] = $element[1];
            else
                throw new \RuntimeException('Callback for Arr::mapAssoc() method must return either an instance of \Ds\Pair or an array like [$key, $value].');

        }

        return $result;
    }

    /**
     * Get keys of an array.
     */
    public static function keys(array $array): array
    {
        return array_keys($array);
    }

    /**
     * Merge two arrays into a new array.
     *
     * Non-array arguments will be wrapped into arrays.
     * Wrapper for array_merge() function.
     */
    public static function merge($first, $second): array
    {
        return array_merge(static::wrap($first), static::wrap($second));
    }

    /**
     * Deduplicate values in an array.
     */
    public static function unique(array $array): array
    {
        return array_unique($array);
    }

    /**
     * Merge two arrays while deduplicating values,
     * i.e. return only unique values.
     *
     * Non-array arguments will be wrapped into arrays.
     *
     * Order of items in the result is not defined.
     */
    public static function mergeUnique($first, $second): array
    {
        return static::unique(static::merge($first, $second));
    }

    /**
     * Get keys of a multidimensional array that are on the specified level.
     *
     * $level = 0 - keys of the specified array itself.
     */
    public static function keysLevelUnique(array $array, int $level = 0): array
    {
        if ($level < 0)
            throw new \RuntimeException('Array level cannot be negative.');

        // Edge case - just get the keys of the array.
        // No deduplication needed - array keys must be unique anyway.
        if ($level === 0)
            return static::keys($array);

        $result = [];

        foreach ($array as $item) {
            if (is_array($item)) {
                $result = static::mergeUnique($result, self::keysLevelUnique($item, $level - 1));
            }
        }

        return $result;
    }

    /**
     * Get all values from an array as an array,
     * i.e. turn an associative array into a numbered one.
     */
    public static function values(array $array): array
    {
        return array_values($array);
    }

    /**
     * Get values from an array while deduplicating them,
     * i.e. turn an associative array into a numbered one
     * with only unique values from the original array.
     */
    public static function uniqueValues(array $array): array
    {
        return static::unique(static::values($array));
    }

    /**
     * Get values of a multidimensional array that are on the specified level.
     *
     * $level = 0 - keys of the specified array itself.
     */
    public static function valuesLevelUnique(array $array, int $level = 0): array
    {
        if ($level < 0)
            throw new \RuntimeException('Array level cannot be negative.');

        // Edge case - just deduplicate the values from the array.
        if ($level === 0)
            return static::uniqueValues($array);

        $result = [];

        foreach ($array as $item) {
            if (is_array($item)) {
                $result = static::mergeUnique($result, self::valuesLevelUnique($item, $level - 1));
            }
        }

        return $result;
    }

    /**
     * Get the length of an array using the built-in count() function.
     *
     * @param bool $recursive Set to true to recursively count the number of items in nested arrays as well.
     */
    public static function length(array $array, bool $recursive = false): int
    {
        return count($array, $recursive ? COUNT_RECURSIVE : COUNT_NORMAL);
    }

    /**
     * Determine if an array is empty, i.e. has exactly 0 elements.
     */
    public static function empty(array $array): bool
    {
        return static::length($array) == 0;
    }

    /**
     * Get the key of the first element of an array.
     */
    public static function firstKey(array $array): mixed
    {
        return static::first(static::keys($array));
    }

    /**
     * Trim the strings in an array.
     *
     * @param string[] $array
     */
    public static function trimValues(array $array): array
    {
        return static::map($array, fn(string $item) => trim($item));
    }

    /**
     * Get the intersection of arrays,
     * i.e. values that are present in both of them.
     *
     * Wrapper for the built-in array_intersect() function.
     */
    public static function intersect(array $first, array $second): array
    {
        return array_intersect($first, $second);
    }

    /**
     * Append items to the end of an array.
     *
     * Wrapper for the built-in array_push() function.
     */
    public static function append(array &$array, ...$items): array
    {
        array_push($array, ...$items);

        return $array;
    }

    /**
     * Filter out empty values using empty() function.
     */
    public static function whereNotEmpty(array $array): array
    {
        return static::filter($array, fn(mixed $item) => ! empty($item));
    }
}
