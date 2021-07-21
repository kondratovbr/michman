<?php declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

// TODO: CRITICAL! Cover with tests!

class UnixPathRule implements Rule
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
        return (bool) preg_match(
            '/^(\/[^\/ ]*)+\/?$/',
            $value
        );
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.custom.path');
    }
}
