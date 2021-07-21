<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\StoreProjectAction;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Project;
use App\Models\Server;
use App\Support\Arr;
use App\Support\Str;
use App\Validation\Fields\SupportedPythonVersionField;
use App\Validation\Rules;
use Ds\Pair;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

// TODO: CRITICAL! CONTINUE.

class CreateProjectForm extends Component
{
    use AuthorizesRequests,
        TrimsInput,
        ListensForEchoes;

    public Server $server;

    public array $types;
    public array $pythonVersions;

    public string $domain = '';
    public string $aliases = '';
    public string $type = 'django';
    public string $root = '/static';
    public string $python_version = '3_9';
    public bool $allow_sub_domains = false;
    public bool $create_database = false;
    public string $db_name = '';

    /** @var string[] */
    protected $listeners = [
        //
    ];

    protected function configureEchoListeners(): void
    {
        //
    }

    protected function prepareForValidation($attributes): array
    {
        if (is_string($attributes['aliases'])) {
            $attributes['aliases'] = Arr::map(
                explode(',', $attributes['aliases']),
                fn(string $domain) => trim($domain)
            );
        }

        if (isset($attributes['root'][0]) && $attributes['root'][0] != '/')
            $attributes['root'] = '/' . $attributes['root'];

        return $attributes;
    }

    protected function rules(): array
    {
        /*
         * TODO: CRITICAL! Error messages here probably suck. Check it out.
         */

        $rules = [
            'domain' => Rules::domain()->required(),
            'aliases' => Rules::array(),
            'aliases.*' => Rules::domain(),
            'type' => Rules::string(1, 16)
                ->in(Arr::keys(config('projects.types')))
                ->required(),
            'root' => Rules::path()->required(),
            'python_version' => SupportedPythonVersionField::new()->required(),
            'allow_sub_domains' => Rules::boolean(),
        ];

        if (optional($this->server)->canCreateDatabase()) {
            $rules['create_database'] = Rules::boolean();
            $rules['db_name'] = Rules::alphaNumDashString(1, 255)
                ->requiredIfAnotherFieldIs('create_database', true);
        }

        return $rules;
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->authorize('create', [Project::class, $this->server]);

        $this->types = Arr::mapKeys(
            config('projects.types'),
            fn(string $type) => __("projects.types.{$type}"),
            true
        );
        $this->pythonVersions = $this->pythonVersions = Arr::mapAssoc(
            Arr::keys(config('servers.python')),
            fn(int $index, string $type) => new Pair($type, Str::replace('_', '.', $type))
        );
    }

    /**
     * Store a new project.
     */
    public function store(StoreProjectAction $storeAction): void
    {
        $validated = $this->validate();

        $this->authorize('create', [Project::class, $this->server]);

        dd($validated);

        //
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('projects.create-project-form');
    }
}
