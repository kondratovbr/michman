<?php declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DomainRule implements Rule
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
        // https://regexr.com/3g5j0
        return preg_match(
            '/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i',
            $value
        );
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.custom.domain');
    }
}