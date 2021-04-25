<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use App\DataTransferObjects\SizeData;
use App\Facades\Auth;
use App\Services\ServerProviderInterface;
use App\Support\Arr;
use App\Validation\Rules;
use Ds\Pair;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\RequestException;
use Livewire\Component;

// TODO: Should probably refactor to use one component class for all providers,
//       but maybe some other dependencies to adapt to their differences.

// TODO: The API key may be read-only, so we should do some POST request at the creation (or afterwards, in the queue) to verify that we have write access. We can just mark the key as "read-only" or just "invalid" in the providers list if we encounter such issue.

// TODO: Note - all server configuration tasks and even the creation itself should be done in the queue to avoid hogging the request - it may take some time. Don't forget to show something meaningful to the user in the meantime.

class DigitalOceanForm extends Component
{
    /** An interface to DigitalOcean API with the currently chosen user's API token. */
    protected ServerProviderInterface $api;

    /** @var int[] User's server providers. */
    public array $providers = [];
    /** @var string[] Regions currently available for server creation based on the data provided. */
    public array $availableRegions = [];
    /** @var string[] Sizes currently available for server creation based on the data provided. */
    public array $availableSizes = [];
    /** @var string[] Server types supported. */
    public array $types = [];

    /** Current user input. */
    public array $state = [
        'provider_id' => null,
        'server_type' => 'app',
        'name' => '',
        'region' => null,
        'size' => null,
        'python_version' => null,
        'database' => null,
        'db_name' => null,
    ];

    /** Error code returned by the external API, if any. */
    public int|null $apiErrorCode = null;

    /**
     * Get the validation rules for user input.
     */
    public function rules(): array
    {
        return [
            'state' => [
                'provider_id' => Rules::integer()->in(Arr::keys($this->providers))->required(),
                'region' => Rules::string(0, 255)->in(Arr::keys($this->availableSizes))->required(),
                'size' => Rules::string(0, 255)->in(Arr::keys($this->availableSizes))->required(),
                'type' => Rules::string(0, 255)->in(Arr::keys(config('servers.types')))->required(),
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
            ->oldest()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->state['provider_id'] = Arr::first(Arr::keys($this->providers));
        $this->loadApi();
        $this->loadProviderData();

        $this->state['name'] = generateRandomName();

        $this->types = Arr::mapAssoc(
            Arr::keys(config('servers.types')),
            fn(int $key, string $type) => new Pair($type, __('servers.types.' . $type . '.name'))
        );
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
                $this->loadApi();
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
        $this->handleApiErrors(function () {
            $this->availableRegions = Arr::pluck(
                $this->api->getAvailableRegions()->toArray(),
                'name',
                'slug'
            );
        });
    }

    /**
     * Load data about a currently selected region.
     */
    protected function loadRegionData(): void
    {
        $this->handleApiErrors(function () {
            $sizes = $this->api->getSizesAvailableInRegion($this->state['region']);

            $this->availableSizes = $sizes->mapWithKeys(fn(SizeData $size) =>
            [$size->slug => trans_choice('account.providers.digital_ocean_v2.size-name',
                $size->cpus,
                [
                    'ramGb' => round($size->memoryMb / 1024, 1),
                    'disk' => sizeForHumansRounded($size->diskGb * 1024 * 1024 * 1024, 1),
                    'price' => $size->priceMonthly,
                ]
            )]
            )->toArray();
        });
    }

    /**
     * Wrap any external API calls into an exception handler to gracefully handle possible errors.
     */
    protected function handleApiErrors(callable $closure): void
    {
        try {
            $closure();
        } catch (RequestException $exception) {
            $this->apiErrorCode = $exception->response->status();
            $this->reset(['state']);
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
