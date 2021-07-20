<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Http\Livewire\Traits\ListensForEchoes;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Project;
use App\Models\Server;
use App\Support\Arr;
use App\Support\Str;
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
    public string $type = 'python';
    public string $root = '/public';
    public string $pythonVersion = '3_9';

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
        $attributes['aliases'] = Arr::map(
            explode(',', $attributes['aliases']),
            fn(string $domain) => trim($domain)
        );

        return $attributes;
    }

    protected function rules(): array
    {
        return [
            'domain' => Rules::domain()->required(),
            'aliases' => Rules::array(),
            'aliases.*' => Rules::domain(),
        ];
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
    public function store(): void
    {
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
