<?php declare(strict_types=1);

namespace App\Rules\Providers;

use App\Services\ServerProviderInterface;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\App;

class ProviderKeyValid implements Rule
{
    public function __construct(
        protected string $providerName,
        protected string|null $secret = null,
    ) {}

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        /** @var ServerProviderInterface $api */
        $api = App::make($this->providerName . '-servers', [
            'key' => $value,
            'secret' => $this->secret ?? ''
        ]);

        return $api->credentialsAreValid();
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.custom.provider-credentials-valid');
    }
}
