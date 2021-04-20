<?php declare(strict_types=1);

namespace App\Http\Livewire\Providers;

use App\Actions\Providers\StoreProviderAction;
use App\DataTransferObjects\ProviderData;
use App\Facades\Auth;
use App\Models\Provider;
use App\Rules\Providers\ProviderKeyValid;
use App\Rules\Providers\ProviderTokenValid;
use App\Support\Arr;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateProviderForm extends Component
{
    use AuthorizesRequests;

    public string $provider = '';
    public string|null $token = null;
    public string|null $key = null;
    public string|null $secret = null;
    public string|null $name = null;

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->provider = (string) config('providers.default');
    }

    /**
     * Remove auth parameters unrelated to the chosen provider.
     */
    protected function prepareForValidation($attributes): mixed
    {
        if ((string) config('providers.list.' . $attributes['provider'] . '.auth_type') == 'token') {
            $attributes['key'] = null;
            $attributes['secret'] = null;
        }

        if ((string) config('providers.list.' . $this->provider . '.auth_type') == 'basic') {
            $attributes['token'] = null;
        }

        return $attributes;
    }

    /**
     * Get the validation rules.
     */
    protected function rules(): array
    {
        /*
         * TODO: IMPORTANT! Write better validation error messages here.
         *       For example, "unique" rules is a major offender - check it out.
         *       Maybe even refactor into a separate Validator class.
         */

        $authType = (string) config('providers.list.' . $this->provider . '.auth_type');

        return [
            'provider' => Rules::string(0, 255)
                // Take the names of all configured providers and filter out the disabled ones.
                ->in(Arr::filter(Arr::keys((array) config('providers.list')),
                    fn($provider) => ! config('providers.list.' . $provider . '.disabled')
                ))
                ->required(),
            'token' => Rules::string()->max(255)->nullable()
                ->requiredIf($authType === 'token')
                ->addRule(
                    Rule::unique(Provider::class, 'token')
                        ->where('user_id', Auth::user()->getKey())
                )->addRuleIf(
                    new ProviderTokenValid($this->provider),
                    $authType === 'token'
                ),
            'key' => Rules::string()->max(255)->nullable()
                ->requiredIf($authType === 'basic')
                ->addRule(
                    Rule::unique(Provider::class, 'key')
                        ->where('user_id', Auth::user()->getKey())
                )->addRuleIf(
                    new ProviderKeyValid($this->provider, $this->secret),
                    $authType === 'basic'
                ),
            'secret' => Rules::string()->max(255)->nullable()
                ->requiredIf($authType === 'basic'),
            // TODO: IMPORTANT! Should the name actually be optional or does UI requires it?
            'name' => Rules::string()->max(255)->nullable(),
        ];
    }

    /**
     * Store a new server provider credentials.
     */
    public function store(StoreProviderAction $action): void
    {
        $this->authorize('create', Provider::class);

        $this->validate();

        $action->execute(new ProviderData(
            owner: Auth::user(),
            provider: $this->provider,
            token: $this->token,
            key: $this->key,
            secret: $this->secret,
            name: $this->name,
        ));

        $this->reset();

        // This event is used to show the success message.
        $this->emit('saved');
        // This event is used to update the providers table.
        $this->emit('provider-stored');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('providers.create-form');
    }
}
