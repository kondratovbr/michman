<?php declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str as IlluminateStr;

/**
 * Custom support class for manipulating strings
 */
class Str extends IlluminateStr
{
    /**
     * Replace substrings of a string with another string.
     *
     * Wrapper for the built-in str_replace() function.
     */
    public static function replace(string $searchFor, string $replaceWith, string $subject, int $count = null): string
    {
        return str_replace($searchFor, $replaceWith, $subject, $count);
    }
}
