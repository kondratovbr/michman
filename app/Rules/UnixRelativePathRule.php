<?php declare(strict_types=1);

namespace App\Rules;

use App\Support\Str;
use Illuminate\Contracts\Validation\Rule;

class UnixRelativePathRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (! is_string($value))
            return false;

        if (Str::length($value) == 0)
            return false;

        if ($value[0] == '/')
            return false;

        $absolute = '/' . $value;

        return UnixAbsolutePathRule::isAbsolutePath($absolute);
    }

    public function message(): string
    {
        return __('validation.custom.path');
    }
}
