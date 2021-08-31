<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;
use App\Actions\Projects\StoreProjectAction;
use App\DataTransferObjects\NewProjectData;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Project;
use App\Models\Server;
use App\Support\Arr;
use App\Support\Str;
use App\Validation\Fields\SupportedPythonVersionField;
use App\Validation\Rules;
use Ds\Pair;
use Illuminate\Contracts\View\View;

// TODO: CRITICAL! Cover with tests.

// TODO: CRITICAL! Make sure the jobs cannot run on an unprepared server. And the whole server view cannot be seen at all as well.

// TODO: CRITICAL! Make sure the main domain is unique server-wide. Maybe also user-wide. Users and some directories on servers are called by the domain name, so it should be unique.

// TODO: CRITICAL! Figure out tasks and maybe statuses for servers/projects during project creation and repo installation.

class CreateProjectForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInputBeforeValidation,
        ListensForEchoes;

    public Server $server;

    public array $types;
    public array $pythonVersions;

    public array $state = [
        'domain' => '',
        'aliases' => '',
        'type' => 'django',
        'pythonVersion' => '3_9',
        'allowSubDomains' => false,
        'createDatabase' => true,
        'dbName' => '',
        'createDbUser' => true,
        'dbUserName' => '',
        'dbUserPassword' => '',
    ];

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
        $state = $attributes['state'];

        foreach (['domain', 'dbUserName'] as $attr) {
            if (is_string($state[$attr])) {
                $state[$attr] = Str::lower($state[$attr]);
            }
        }

        if (is_string($state['aliases'])) {
            $state['aliases'] = Arr::map(
                explode(',', Str::lower($state['aliases'])),
                fn(string $domain) => trim($domain)
            );
        }

        $attributes['state'] = $state;

        return $attributes;
    }

    protected function rules(): array
    {
        /*
         * TODO: CRITICAL! Error messages here probably suck. Check it out.
         *       Seems like ValidatesInput trait should have handled it. Why doesn't it work?
         */

        $rules = [
            'state.domain' => Rules::domain()->required(),
            'state.aliases' => Rules::array()->nullable(),
            'state.aliases.*' => Rules::domain(),
            'state.type' => Rules::string(1, 16)
                ->in(Arr::keys(config('projects.types')))
                ->required(),
            'state.pythonVersion' => SupportedPythonVersionField::new()->required(),
            'state.allowSubDomains' => Rules::boolean(),
        ];

        if (optional($this->server)->canCreateDatabase()) {
            $rules['state.createDatabase'] = Rules::boolean();
            $rules['state.dbName'] = Rules::alphaNumDashString(1, 255)
                ->requiredIfAnotherFieldIs('state.createDatabase', true)
                ->nullable();
        }

        if (optional($this->server)->canCreateDatabaseUser() && $this->state['createDatabase']) {
            $rules['state.createDbUser'] = Rules::boolean();
            $rules['state.dbUserName'] = Rules::alphaNumDashString(1, 255)
                ->requiredIfAnotherFieldIs('state.createDbUser', true)
                ->nullable();
            $rules['state.dbUserPassword'] = Rules::alphaNumDashString(1, 255)
                ->requiredIfAnotherFieldIs('state.createDbUser', true)
                ->nullable();
        }

        return $rules;
    }

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
        $validated = $this->validate()['state'];

        $this->authorize('create', [Project::class, $this->server]);

        $storeAction->execute(new NewProjectData(
            domain: $validated['domain'],
            aliases: $validated['aliases'] ?? [],
            type: $validated['type'],
            python_version: $validated['pythonVersion'] ?? null,
            allow_sub_domains: $validated['allowSubDomains'],
            create_database: $validated['createDatabase'] ?? false,
            db_name: $validated['dbName'] ?? null,
            create_db_user: $validated['createDbUser'] ?? false,
            db_user_name: $validated['dbUserName'] ?? null,
            db_user_password: $validated['dbUserPassword'] ?? null,
        ), $this->server);

        $this->reset('state');

        $this->emit('project-stored');
    }

    public function render(): View
    {
        return view('projects.create-project-form');
    }
}
