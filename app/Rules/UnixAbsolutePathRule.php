<?php declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UnixAbsolutePathRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (! is_string($value))
            return false;

        return static::isAbsolutePath($value);
    }

    public function message(): string
    {
        return __('validation.custom.path');
    }

    /** Reusable method to check that string is in fact a valid absolute path. */
    public static function isAbsolutePath(string $value): bool
    {
        // https://regex101.com/r/JNM8TK/2
        return (bool) preg_match(
            '/^(\/[^\/ ]*)+\/?$/',
            $value
        );
    }
}
