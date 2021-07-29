<?php declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

// TODO: CRITICAL! Cover with tests!

class UnixAbsolutePathRule implements Rule
{
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        return static::isAbsolutePath($value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.custom.path');
    }

    /**
     * Reusable method to check that string is in fact a valid absolute path.
     */
    public static function isAbsolutePath(string $value): bool
    {
        // https://regex101.com/r/JNM8TK/2
        return (bool) preg_match(
            '/^(\/[^\/ ]*)+\/?$/',
            $value
        );
    }
}
