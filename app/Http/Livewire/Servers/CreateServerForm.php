<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use App\Facades\Auth;
use App\Support\Arr;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/*
 * TODO: IMPORTANT! Opening the actual form still may take long time for some users.
 *       That's because the cached sizes and regions data needs to be updated regularly.
 *       Should figure out how to do it in a scheduled job.
 */

/**
 * @property-read string $formComponent
 */
class CreateServerForm extends Component
{
    /** Currently chosen server provider type. */
    public string $provider = '';

    /** @var string[] Providers supported by the app where the user have credentials. */
    public array $availableProviders = [];

    /** @var string[] Provider name to form component mapping. */
    private array $formComponents = [
        'digital_ocean_v2' => 'servers.digital-ocean-form',
    ];

    /** @var string[] */
    protected $listeners = ['server-created' => 'cancel'];

    public function rules(): array
    {
        return [
            'provider' => Rules::string(1, 255)->in($this->availableProviders)->required(),
        ];
    }

    public function mount(): void
    {
        $usersProviders = user()->providers()
            ->pluck('provider')
            ->unique();

        $this->availableProviders = Arr::keys(Arr::filterAssoc(
            config('providers.list'),
            fn(string $provider, array $config) =>
                ! $config['disabled'] && $usersProviders->contains($provider)
        ));
    }

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    /** Trigger an underlying server creation form to store a new server. */
    public function store(): void
    {
        $this->validate();

        $this->emitTo($this->formComponent, 'store-server-button-pressed');
    }

    /** Cancel server creation and reset the form. */
    public function cancel(): void
    {
        $this->reset('provider');
    }

    /** Get the name of a server creation form component for a chosen provider. */
    public function getFormComponentProperty(): string|null
    {
        // User just loaded this component and haven't chosen a provider yet.
        if ($this->provider === '')
            return null;

        // Shouldn't happen, so we gracefully fail but log an error and don't just throw the user to an error page.
        if (! isset($this->formComponents[$this->provider])) {
            Log::error('Tried to render a server creation form for an undeclared provider.');
            return null;
        }

        return $this->formComponents[$this->provider];
    }

    public function render(): View
    {
        return view('servers.create-server-form');
    }
}
