<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use App\DataTransferObjects\SizeData;
use App\Facades\Auth;
use App\Models\Provider;
use App\Services\ServerProviderInterface;
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
    protected ServerProviderInterface $api;

    public array $providers;
    public array $availableRegions = [];
    public array $availableSizes = [];

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
                'provider_id' => Rules::integer()->in(Arr::keys($this->providers))->required(),
                'region' => Rules::string()->in(Arr::keys($this->availableSizes))->required(),
                'size' => Rules::string()->in(Arr::keys($this->availableSizes))->required(),
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
            ->latest()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->state['provider_id'] = Arr::first(Arr::keys($this->providers));
        $this->loadApi();
        $this->loadProviderData();
    }

    /**
     * Prepare the component before each request.
     */
    public function hydrate(): void
    {
        $this->loadApi();
    }

    /**
     * Runs after a property is changed.
     */
    public function updated(string $name, mixed $value): void
    {
        switch ($name) {
            case 'state.provider_id':
                $this->validateOnly('state.provider_id');
                $this->loadProviderData();
                break;
            case 'state.region':
                $this->validateOnly('state.region');
                $this->loadRegionData();
                break;
        }
    }

    /**
     * Create and store inside this component an instance of an API handler.
     */
    protected function loadApi(): void
    {
        if (isset($this->state['provider_id'])) {
            $this->api = Auth::user()->providers()
                ->whereKey($this->state['provider_id'])
                ->first()
                ->api();
        }
    }

    /**
     * Load data about a current provider.
     */
    protected function loadProviderData(): void
    {
        if (isset($this->api)) {
            $this->availableRegions = Arr::pluck(
                $this->api->getAvailableRegions()->toArray(),
                'name',
                'slug'
            );
        }
    }

    /**
     * Load data about a currently selected region.
     */
    protected function loadRegionData(): void
    {
        $sizes = $this->api->getSizesAvailableInRegion($this->state['region']);

        $this->availableSizes = Arr::pluck(
            $sizes->map(fn(SizeData $size) =>
                //
            ),
            'name',
            'slug'
        );
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('servers.digital-ocean-form');
    }
}
