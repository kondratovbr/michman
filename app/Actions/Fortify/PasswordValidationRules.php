<?php declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Validation\Rules;
use Illuminate\Contracts\Support\Arrayable;
use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /** Get the validation rules used to validate passwords. */
    protected function passwordRules(): Arrayable|array
    {
        return Rules::string()
            ->addRule((new Password)
                ->length((int) config('auth.password.min_length'))
                ->withMessage(trans_choice('errors.new_password', (int) config('auth.password.min_length')))
            )
            ->max((int) config('auth.password.max_length'))
            ->requiredWithoutAny(['oauth_provider', 'oauth_id'])
            ->confirmed();
    }
}
