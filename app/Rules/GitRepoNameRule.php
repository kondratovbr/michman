<?php declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GitRepoNameRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (! is_string($value))
            return false;

        // https://regex101.com/r/4FVHDk/1
        return (bool) preg_match('/^[a-zA-Z0-9_\/]*$/', $value);
    }

    public function message(): string
    {
        return __('validation.custom.repo');
    }
}
