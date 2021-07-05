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
}
