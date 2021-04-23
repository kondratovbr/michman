<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use App\Facades\Auth;
use App\Models\Provider;
use App\Support\Arr;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Livewire\Component;

// TODO: Should probably refactor to use one component class for all providers,
//       but maybe some other dependencies to adapt to their differences.

// TODO: The API key may be read-only, so we should do some POST request at the creation (or afterwards, in the queue) to verify that we have write access. We can just mark the key as "read-only" or just "invalid" in the providers list if we encounter such issue.

// TODO: Note - all server configuration tasks and even the creation itself should be done in the queue to avoid hogging the request - it may take some time. Don't forget to show something meaningful to the user in the meantime.

class DigitalOceanForm extends Component
{
    public array $providers;
    public array $availableRegions = [];
    public array $availableSizes = [];

    public array $foobar = ['foo' => 'bar'];

    public array $state = [
        'provider_id' => null,
        'server_type' => 'app',
        'name' => '',
        'region' => '',
        'size' => '',
        'python_version' => '',
        'database' => null,
        'db_name' => '',
    ];

    public function rules(): array
    {
        return [
            'state' => [
                'provider_id' => Rules::integer()->in(Arr::keys($this->providers)),
            ],
        ];
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->providers = Auth::user()->providers()
            ->where('provider', 'digital_ocean_v2')
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getAvailableRegionsProperty(): array
    {
        return Arr::pluck($this->availableRegions, 'name', 'slug');
    }

    /**
     * Runs after a property is changed.
     */
    public function updated(string $name, mixed $value): void
    {
        if ($name === 'state.provider_id') {
            $this->validateOnly('state.provider_id');
            $this->loadProviderData();
            // Alpine will listen for this event and update the value on the frontend
            $this->emit('state.provider_id-changed', $value);
        }
    }

    /**
     * Load data about a current provider.
     */
    protected function loadProviderData(): void
    {
        /** @var Provider $provider */
        $provider = Auth::user()->providers()
            ->whereKey($this->state['provider_id'])
            ->first();

        if ($provider != null) {
            $this->availableRegions = $provider->api()->getAvailableRegions()->toArray();
            $this->foobar = Arr::pluck($this->availableRegions, 'name', 'slug');
            // Tell Alpine to re-initialize the region select component when we change the options available
            $this->emit('regions-changed');
        }
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('servers.digital-ocean-form');
    }
}
