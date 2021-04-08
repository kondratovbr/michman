<?php declare(strict_types=1);

namespace App\Rules;

use App\Services\ServerProviderInterface;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\App;

// TODO: CONTINUE!

class ProviderCredentialsValid implements Rule
{
    protected ServerProviderInterface $api;
    private string $providerName;

    public function __construct(string $providerName)
    {
        $this->providerName = $providerName;
        $this->api = App::make($providerName);
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        return $this->
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.custom.provider-credentials-valid');
    }
}
