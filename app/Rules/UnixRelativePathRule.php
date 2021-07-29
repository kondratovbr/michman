<?php declare(strict_types=1);

namespace App\Rules;

use App\Support\Str;
use Illuminate\Contracts\Validation\Rule;

// TODO: CRITICAL! Cover with tests!

class UnixRelativePathRule implements Rule
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
        if (Str::length($value) == 0)
            return false;

        if ($value[0] == '/')
            return false;

        $absolute = '/' . $value;

        return UnixAbsolutePathRule::isAbsolutePath($absolute);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.custom.path');
    }
}
