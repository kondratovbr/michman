<?php declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str as IlluminateStr;

/**
 * Custom support class for manipulating strings
 */
class Str extends IlluminateStr
{
    /**
     * Split a string by all possible end-of-line characters.
     *
     * @return string[]
     */
    public static function splitLines(string $string): array
    {
        return preg_split("/\r\n|\n|\r/", $string);
    }

    /** Re-implement the built-in implode() function to support other iterable objects, like Ds/Set for example. */
    public static function implode(string $separator, \Traversable|array|null $array): string
    {
        if (is_null($array))
            return '';

        if (is_array($array))
            $array = new \ArrayIterator($array);

        $result = null;

        foreach ($array as $item) {
            if (is_null($result))
                $result = $item;
            else
                $result .= $separator . $item;
        }

        return $result ?? '';
    }

    /**
     * Determine if a given string contains a given substring,
     * disregarding casing.
     */
    public static function containsLax(string $haystack, string|array $needles): bool
    {
        $haystack = Str::lower($haystack);
        $needles = Arr::map(Arr::wrap($needles), fn($item) => Str::lower($item));

        return Str::contains($haystack, $needles);
    }
}
