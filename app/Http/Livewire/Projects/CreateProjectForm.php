<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;
use App\Actions\Projects\StoreProjectAction;
use App\DataTransferObjects\NewProjectData;
use App\Facades\Auth;
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


/*
 * TODO: CRITICAL! CONTINUE. Finish the front-end part (don't forget the DB user password field) and test everything, then continue with the repo installation.
 */

class CreateProjectForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInputBeforeValidation,
        ListensForEchoes;

    public Server $server;

    public array $types;
    public array $pythonVersions;

    public string $domain = '';
    public string $aliases = '';
    public string $type = 'django';
    public string $python_version = '3_9';
    public bool $allow_sub_domains = true;
    public bool $create_database = true;
    public string $db_name = '';
    public bool $create_db_user = true;
    public string $db_user_name = '';

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
        if (is_string($attributes['domain'])) {
            $attributes['domain'] = Str::lower($attributes['domain']);
        }

        if (is_string($attributes['aliases'])) {
            $attributes['aliases'] = Arr::map(
                explode(
                    ',',
                    Str::lower($attributes['aliases'])
                ),
                fn(string $domain) => trim($domain)
            );
        }

        return $attributes;
    }

    protected function rules(): array
    {
        /*
         * TODO: CRITICAL! Error messages here probably suck. Check it out.
         *       Seems like ValidatesInput trait should have handled it. Why doesn't it work?
         */

        $rules = [
            'domain' => Rules::domain()->required(),
            'aliases' => Rules::array()->nullable(),
            'aliases.*' => Rules::domain(),
            'type' => Rules::string(1, 16)
                ->in(Arr::keys(config('projects.types')))
                ->required(),
            'python_version' => SupportedPythonVersionField::new()->required(),
            'allow_sub_domains' => Rules::boolean(),
        ];

        if (optional($this->server)->canCreateDatabase()) {
            $rules['create_database'] = Rules::boolean();
            $rules['db_name'] = Rules::alphaNumDashString(1, 255)
                ->requiredIfAnotherFieldIs('create_database', true)
                ->nullable();
        }

        if (optional($this->server)->canCreateDatabaseUser() && $this->create_database) {
            $rules['create_db_user'] = Rules::boolean();
            $rules['db_user_name'] = Rules::alphaNumDashString(1, 255)
                ->requiredIfAnotherFieldIs('create_db_user', true)
                ->nullable();
            $rules['db_user_password'] = Rules::alphaNumDashString(1, 255)
                ->requiredIfAnotherFieldIs('create_db_user', true)
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
        $validated = $this->validate();

        $this->authorize('create', [Project::class, $this->server]);

        $storeAction->execute(new NewProjectData(
            domain: $validated['domain'],
            aliases: $validated['aliases'] ?? [],
            type: $validated['type'],
            python_version: $validated['python_version'] ?? null,
            allow_sub_domains: $validated['allow_sub_domains'],
            create_database: $validated['create_database'] ?? false,
            db_name: $validated['db_name'] ?? null,
            create_db_user: $validated['create_db_user'] ?? false,
            db_user_name: $validated['db_user_name'],
            db_user_password: $validated['db_user_password'],
        ), Auth::user(), $this->server);

        $this->reset(
            'domain',
            'aliases',
            'type',
            'python_version',
            'allow_sub_domains',
            'create_database',
            'db_name',
            'create_db_user',
            'db_user_name',
            'db_user_password',
        );

        $this->emit('project-stored');
    }

    public function render(): View
    {
        return view('projects.create-project-form');
    }
}
