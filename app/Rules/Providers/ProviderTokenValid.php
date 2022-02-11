<?php declare(strict_types=1);

namespace App\Rules\Providers;

use App\Services\ServerProviderInterface;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\App;

class ProviderTokenValid implements Rule
{
    public function __construct(
        protected string $providerName,
    ) {}

    /** Determine if the validation rule passes. */
    public function passes($attribute, $value): bool
    {
        if (config('providers.list.' . $this->providerName . '.disabled'))
            return false;

        /** @var ServerProviderInterface $api */
        $api = App::make("{$this->providerName}_servers", ['token' => $value]);

        return $api->credentialsAreValid();
    }

    /** Get the validation error message. */
    public function message(): string
    {
        return __('validation.custom.provider-credentials-valid');
    }
}
