<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use App\Actions\Servers\StoreServerAction;
use App\DataTransferObjects\NewServerData;
use App\DataTransferObjects\SizeData;
use App\Facades\Auth;
use App\Models\Server;
use App\Services\ServerProviderInterface;
use App\Support\Arr;
use App\Support\Str;
use App\Validation\Fields\SupportedPythonVersionField;
use App\Validation\Rules;
use Ds\Pair;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Client\RequestException;
use Livewire\Component;

// TODO: Should probably refactor to use one component class for all providers, but maybe some other dependencies to adapt to their differences.

// TODO: The API key may be read-only, so we should do some POST request at the creation (or afterwards, in the queue) to verify that we have write access. We can just mark the key as "read-only" or just "invalid" in the providers list if we encounter such issue.

// TODO: Note - all server configuration tasks and even the creation itself should be done in the queue to avoid hogging the request - it may take some time. Don't forget to show something meaningful to the user in the meantime.

// TODO: This form should probably be horizontal, like Forge does.

// TODO: CRITICAL! Unfinished! Doesn't react after a server is created, nothing gets updated on the page.

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
        'db_name' => null,
        'cache' => 'redis',
        'add_ssh_key_to_vcs' => true,
    ];

    /** Error code returned by the external API, if any. */
    public int|null $apiErrorCode = null;

    /** @var string[] */
    protected $listeners = ['store-server-button-pressed' => 'store'];

    /**
     * Get the validation rules for user input.
     */
    public function rules(): array
    {
        /*
         * TODO: CRITICAL! Error messages here suck. They all display the field name like "state.provider id",
         *       which is bullshit. Fix.
         */

        $rules = [
            'state.provider_id' => Rules::integer()
                ->in(Arr::keys($this->providers))
                ->required(),
            // TODO: CRITICAL! Make the name unique for a user.
            'state.name' => Rules::string(1, 255)->required(),
            'state.region' => Rules::string(1, 255)
                ->in(Arr::keys($this->availableRegions))
                ->required(),
            'state.size' => Rules::string(1, 255)
                ->in(Arr::keys($this->availableSizes))
                ->required(),
            'state.type' => Rules::string(1, 255)
                ->in(Arr::keys(config('servers.types')))
                ->required(),
            'state.add_ssh_key_to_vcs' => Rules::boolean()->required(),
        ];

        if ($this->shouldInstall('python')) {
            $rules['state.python_version'] = SupportedPythonVersionField::new()->required();
        }

        if ($this->shouldInstall('database')) {
            $rules['state.database'] = Rules::string(1, 32)
                ->in(Arr::keys(Arr::add(config('servers.databases'), 'none', null)))
                ->required();
            if ($this->state['database'] != 'none')
                $rules['state.db_name'] = Rules::string(1, 255)->required();
        }

        if ($this->shouldInstall('cache')) {
            $rules['state.cache'] = Rules::string(1, 32)
                ->in(Arr::keys(Arr::add(config('servers.caches'), 'none', null)))
                ->required();
        }

        return $rules;
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

        $this->types = Arr::mapAssoc(
            Arr::keys(config('servers.types')),
            fn(int $index, string $type) => new Pair($type, __('servers.types.' . $type . '.name'))
        );
        $this->databases = Arr::mapAssoc(
            Arr::keys(Arr::add(config('servers.databases'), 'none', null)),
            fn(int $index, string $type) => new Pair($type, __('servers.databases.' . $type))
        );
        $this->pythonVersions = Arr::mapAssoc(
            Arr::keys(config('servers.python')),
            fn(int $index, string $type) => new Pair($type, Str::replace('_', '.', $type))
        );
        $this->caches = Arr::mapAssoc(
            Arr::keys(Arr::add(config('servers.caches'), 'none', null)),
            fn(int $index, string $type) => new Pair($type, __('servers.caches.' . $type))
        );

        $this->loadDefaults();
    }

    /**
     * Put default values for user inputs.
     */
    protected function loadDefaults(): void
    {
        $this->state['provider_id'] = Arr::first(Arr::keys($this->providers));
        $this->loadApi();
        $this->loadProviderData();

        // We generate these defaults after the first API operation,
        // because our API error handling code resets the state on errors.
        $this->state['name'] = generateRandomName();
        $this->state['db_name'] = Str::kebab(config('app.name'));

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
                $this->state['add_ssh_key_to_vcs'] = (bool) config('servers.types.' . $this->state['type'] . '.add_ssh_key_to_vcs');
                break;
        }
    }

    /**
     * Create and store inside this component an instance of an API handler.
     */
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

            $this->state['region'] = Arr::firstKey($this->availableRegions);
            $this->loadRegionData();
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
            $this->state['name'] = generateRandomName();
        }
    }

    /**
     * Determine if we should install a certain program based on the currently chosen server type.
     */
    public function shouldInstall(string $program): bool
    {
        if (! Arr::has(config('servers.types'), $this->state['type']))
            return false;

        return Arr::hasValue(config('servers.types.' . $this->state['type'] . '.install'), $program);
    }

    /**
     * Validate and store a new server.
     */
    public function store(StoreServerAction $action): void
    {
        $this->authorize('create', Server::class);

        $this->validate();

        $server = $action->execute(new NewServerData(
            provider: Auth::user()->providers()->findOrFail($this->state['provider_id']),
            name: $this->state['name'],
            region: $this->state['region'],
            size: $this->state['size'],
            type: $this->state['type'],
            pythonVersion: $this->state['python_version'],
            database: $this->state['database'],
            dbName: $this->state['db_name'],
            cache: $this->state['cache'],
            addSshKeyToVcs: $this->state['add_ssh_key_to_vcs'],
        ), Auth::user());

        // dd($server);

        // TODO: CRITICAL! UNFINISHED! Don't forget to provide some feedback on success or failure. And don't forget to show the sudo password to the user!
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('servers.digital-ocean-form');
    }
}
