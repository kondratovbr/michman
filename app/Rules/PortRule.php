<?php declare(strict_types=1);

namespace App\Rules;

use App\Support\Str;
use Illuminate\Contracts\Validation\Rule;

// TODO: CRITICAL! Cover with tests.

class PortRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        if (is_int($value))
            return $value >= 0 && $value <= 65535;

        if (! is_string($value))
            return false;

        $value = trim($value);

        if (Str::contains($value, ':'))
            return $this->validateRange($value);

        return $this->validatePort($value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.custom.port');
    }

    protected function validatePort(string $value): bool
    {
        if (preg_match('/^[0-9]{1,5}$/', $value) !== 1)
            return false;

        return (int) $value <= 65535;
    }

    protected function validateRange(string $value): bool
    {
        [$start, $end] = explode(':', $value, 2);

        if (! ($this->validatePort($start) && $this->validatePort($end)))
            return false;

        return (int) $start <= (int) $end;
    }
}
