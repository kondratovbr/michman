<?php declare(strict_types=1);

namespace App\Http\Livewire\Providers;

use App\Actions\Providers\StoreProviderAction;
use App\DataTransferObjects\ProviderData;
use App\Facades\Auth;
use App\Models\Provider;
use App\Rules\ProviderKeyValid;
use App\Rules\ProviderTokenValid;
use App\Support\Arr;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateForm extends Component
{
    public string $provider = '';
    public string|null $token = null;
    public string|null $key = null;
    public string|null $secret = null;
    public string|null $name = null;

    /**
     * Prepare the component.
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
                ->in(Arr::keys((array) config('providers.list')))
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
            'name' => Rules::string()->max(255)->nullable(),
        ];
    }

    /**
     * Store a new server provider credentials.
     */
    public function store(StoreProviderAction $action): void
    {
        // TODO: CRITICAL! Don't forget authentication and authorization! Have I forget it in other Livewire components?

        $this->validate();

        $action->execute(ProviderData::create(
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
