<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use App\Actions\Servers\StoreServerAction;
use App\DataTransferObjects\NewServerDto;
use App\DataTransferObjects\SizeDto;
use App\Facades\Auth;
use App\Models\Server;
use App\Services\ServerProviderInterface;
use App\Support\Arr;
use App\Support\Str;
use App\Validation\Fields\SupportedPythonVersionField;
use App\Validation\Rules;
use Ds\Pair;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Client\RequestException;
use Illuminate\Validation\Rule;
use Livewire\Component;

// TODO: Should probably refactor to use one component class for all providers, but maybe some other dependencies to adapt to their differences.

class DigitalOceanForm extends Component
{
    use AuthorizesRequests;

    /** An interface to DigitalOcean API with the currently chosen user's API token. */
    protected ServerProviderInterface|null $api;

    /** @var int[] User's server providers. */
    public array $providers = [];
    /** @var string[] Regions currently available for server creation based on the data provided. */
    public array $availableRegions = [];
    /** @var string[] Sizes currently available for server creation based on the data provided. */
    public array $availableSizes = [];

    /** @var string[] Server types supported. */
    public array $types = [];
    /** @var string[] Databases supported. */
    public array $databases = [];
    /** @var string[] Supported versions of Python. */
    public array $pythonVersions = [];
    /** @var string[] Supported types of caches. */
    public array $caches = [];

    /** Current user input. */
    public array $state = [
        'provider_id' => null,
        'name' => null,
        'region' => null,
        'size' => null,
        'type' => 'app',
        'python_version' => '3_9',
        'database' => 'mysql-8_0',
        'cache' => 'redis',
    ];

    /** Error code returned by the external API, if any. */
    public int|null $apiErrorCode = null;

    public bool $successModalOpen = false;
    /** @var Server|null The newly created Server model. */
    public Server|null $server = null;

    /** @var string[] */
    protected $listeners = ['store-server-button-pressed' => 'store'];

    public function rules(): array
    {
        $rules = [
            'state.provider_id' => Rules::integer()
                ->in(Arr::keys($this->providers))
                ->required(),
            'state.name' => Rules::string(1, 255)
                ->addRule(Rule::unique('providers', 'name')->where(
                    fn(Builder $query) => $query->where('user_id', Auth::user()->getKey())
                ))
                ->required(),
            'state.region' => Rules::string(1, 255)
                ->in(Arr::keys($this->availableRegions))
                ->required(),
            'state.size' => Rules::string(1, 255)
                ->in(Arr::keys($this->availableSizes))
                ->required(),
            'state.type' => Rules::string(1, 255)
                ->in(Arr::keys($this->getServerTypeOptions()))
                ->required(),
        ];

        if ($this->shouldInstall('python')) {
            $rules['state.python_version'] = SupportedPythonVersionField::new()->required();
        }

        if ($this->shouldInstall('database')) {
            $rules['state.database'] = Rules::string(1, 32)
                ->in(Arr::keys($this->getDatabaseOptions()))
                ->required();
        }

        if ($this->shouldInstall('cache')) {
            $rules['state.cache'] = Rules::string(1, 32)
                ->in(Arr::keys($this->getCacheOptions()))
                ->required();
        }

        return $rules;
    }

    public function mount(): void
    {
        $this->providers = Auth::user()->providers()
            ->where('provider', 'digital_ocean_v2')
            ->oldest()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->types = $this->getServerTypeOptions();
        $this->databases = $this->getDatabaseOptions();
        $this->pythonVersions = Arr::mapAssoc(
            Arr::keys(config('servers.python')),
            fn(int $index, string $type) => new Pair($type, Str::replace('_', '.', $type))
        );
        $this->caches = $this->getCacheOptions();

        $this->loadDefaults();
    }

    protected function getDatabaseOptions(): array
    {
        $databases = Arr::keys(Arr::filter(
            config('servers.databases'),
            fn(array $config) => $config['enabled'] ?? false,
        ));

        $databases[] = 'none';

        return Arr::mapAssoc($databases,
            fn(int $index, string $type) => new Pair($type, __('servers.databases.' . $type))
        );
    }

    protected function getCacheOptions(): array
    {
        $caches = Arr::keys(Arr::filter(
            config('servers.caches'),
            fn(array $config) => $config['enabled'] ?? false,
        ));

        $caches[] = 'none';

        return Arr::mapAssoc($caches,
            fn(int $index, string $type) => new Pair($type, __('servers.caches.' . $type))
        );
    }

    protected function getServerTypeOptions(): array
    {
        $types = Arr::keys(Arr::filter(config('servers.types'),
            fn(array $config) => $config['enabled'] ?? false,
        ));

        return Arr::mapAssoc($types,
            fn(int $index, string $type) => new Pair($type, __('servers.types.' . $type . '.name'))
        );
    }

    /** Put default values for user inputs. */
    protected function loadDefaults(): void
    {
        $this->state['provider_id'] = Arr::first(Arr::keys($this->providers));
        $this->loadApi();
        $this->loadProviderData();

        // We generate these defaults after the first API operation,
        // because our API error handling code resets the state on errors.
        $this->state['name'] = generateRandomName();

        if ($this->apiErrorCode) {
            // API returned some error, so we stop and put blank values as defaults,
            // so the user can handle it from here.
            $this->state['provider_id'] = null;
            $this->availableRegions = [];
            $this->availableSizes = [];
            $this->apiErrorCode = null;
            return;
        }

        $this->state['region'] = Arr::firstKey($this->availableRegions);
        $this->loadRegionData();

        $this->state['size'] = Arr::firstKey($this->availableSizes);
    }

    public function hydrate(): void
    {
        $this->loadApi();
    }

    public function updated(string $name, mixed $value): void
    {
        switch ($name) {
            case 'state.provider_id':
                $this->validateOnly('state.provider_id');
                $this->apiErrorCode = null;
                $this->loadApi();
                $this->loadProviderData();
                break;
            case 'state.region':
                $this->validateOnly('state.region');
                $this->loadRegionData();
                break;
            case 'state.type':
                $this->validateOnly('type');
                break;
        }
    }

    /** Create and store inside this component an instance of an API handler. */
    protected function loadApi(): void
    {
        if (isset($this->state['provider_id'])) {
            $this->api = optional(
                Auth::user()->providers()
                    ->whereKey($this->state['provider_id'])
                    ->first()
                )->api();
        }
    }

    /** Load data about a current provider. */
    protected function loadProviderData(): void
    {
        $this->handleApiErrors(function () {
            $this->availableRegions = Arr::pluck(
                $this->api->getAvailableRegions()->toArray(),
                'name',
                'slug'
            );

            $this->state['region'] = Arr::firstKey($this->availableRegions);
            $this->loadRegionData();
        });
    }

    /** Load data about a currently selected region. */
    protected function loadRegionData(): void
    {
        $this->handleApiErrors(function () {
            $sizes = $this->api->getSizesAvailableInRegion($this->state['region']);

            $this->availableSizes = $sizes->mapWithKeys(fn(SizeDto $size) =>
            [$size->slug => $size->description == ''
                ? trans_choice('account.providers.digital_ocean_v2.size-name',
                    $size->cpus,
                    [
                        'ramGb' => round($size->memoryMb / 1024, 1),
                        'disk' => sizeForHumansRounded($size->diskGb * 1024 * 1024 * 1024, 1),
                        'price' => $size->priceMonthly,
                    ]
                )
                : trans_choice('account.providers.digital_ocean_v2.size-name-description',
                    $size->cpus,
                    [
                        'ramGb' => round($size->memoryMb / 1024, 1),
                        'disk' => sizeForHumansRounded($size->diskGb * 1024 * 1024 * 1024, 1),
                        'price' => $size->priceMonthly,
                        'description' => $size->description,
                    ]
                )
            ]
            )->toArray();

            $this->state['size'] = Arr::firstKey($this->availableSizes);
        });
    }

    /** Wrap any external API calls into an exception handler to gracefully handle possible errors. */
    protected function handleApiErrors(callable $closure): mixed
    {
        try {
            return $closure();
        } catch (RequestException $exception) {
            $this->apiErrorCode = $exception->response->status();
            $this->reset(['state']);
            $this->state['name'] = generateRandomName();
        }

        return null;
    }

    /** Determine if we should install a certain program based on the currently chosen server type. */
    public function shouldInstall(string $program): bool
    {
        if (! Arr::has(config('servers.types'), $this->state['type']))
            return false;

        return Arr::hasValue(config('servers.types.' . $this->state['type'] . '.install'), $program);
    }

    /** Check if the chosen server size has enough RAM for the chosen type. */
    public function hasEnoughRam(): bool
    {
        // Only 'app' type has any limitations at the moment.
        if ($this->state['type'] != 'app')
            return false;

        return $this->handleApiErrors(function (): bool {
            /** @var SizeDto $size */
            $size = $this->api->getSizesAvailableInRegion($this->state['region'])
                ->firstWhere('slug', $this->state['size']);

            return $size->memoryMb > 1000;
        });
    }

    /** Validate and store a new server. */
    public function store(StoreServerAction $action): void
    {
        $this->authorize('create', Server::class);

        $state = $this->validate()['state'];

        if (! $this->hasEnoughRam())
            return;

        $this->server = $action->execute(
            NewServerDto::fromArray($state),
            Auth::user()->providers()->findOrFail($this->state['provider_id']),
        );

        $this->successModalOpen = true;
    }

    /** Reset the form when user closes the success modal. */
    public function updatedSuccessModalOpen(): void
    {
        if ($this->successModalOpen)
            return;

        $this->emitUp('server-created');
    }

    public function render(): View
    {
        return view('servers.digital-ocean-form');
    }
}
