<?php declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

// TODO: CRITICAL! Cover with tests!

class DomainRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (! is_string($value))
            return false;

        // https://regexr.com/3g5j0
        return (bool) preg_match(
            '/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i',
            $value
        );
    }

    public function message(): string
    {
        return __('validation.custom.domain');
    }
}
