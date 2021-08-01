<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\InstallProjectRepoAction;
use App\Broadcasting\UserChannel;
use App\DataTransferObjects\ProjectRepoData;
use App\Events\VcsProviders\VcsProviderCreatedEvent;
use App\Events\VcsProviders\VcsProviderDeletedEvent;
use App\Events\VcsProviders\VcsProviderUpdatedEvent;
use App\Facades\Auth;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Project;
use App\Models\Server;
use App\Support\Str;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! Cover with tests.

class InstallRepoForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInput,
        ListensForEchoes;

    public Server $server;
    public Project $project;

    public Collection $vcsProviders;

    public array $state = [
        'vcsProviderKey' => null,
        'repo' => '',
        'branch' => 'main',
        'package' => '',
        'root' => '/static',
        'installDependencies' => true,
        'useDeployKey' => null,
        'customPackage' => false,
        'requirementsFile' => 'requirements.txt',
    ];

    /** @var string[] */
    protected $listeners = [
        //
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            UserChannel::name(Auth::user()),
            [
                VcsProviderCreatedEvent::class,
                VcsProviderUpdatedEvent::class,
                VcsProviderDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    protected function prepareForValidation($attributes): array
    {
        $pathAttributes = ['package', 'root', 'requirementsFile'];
        foreach ($pathAttributes as $attr) {
            $attributes['state'][$attr] = trimRelativePath($attributes['state'][$attr]);
        }

        return $attributes;
    }

    public function rules(): array
    {
        /*
         * TODO: CRITICAL! Error messages here suck. Check it out. Check out all forms that use $state - the attribute names are all wrong like "state.repo" - should fix it. In other forms as well.
         */

        return [
            'state.vcsProviderKey' => Rules::integer()->in(Auth::user()->vcsProviders->modelKeys())->required(),
            'state.repo' => Rules::gitRepoName()->required(),
            'state.branch' => Rules::alphaNumDashString(1, 255)->required(),
            'state.package' => Rules::relativePath()->required(),
            'state.root' => Rules::relativePath()->required(),
            'state.installDependencies' => Rules::boolean(),
            'state.requirementsFile' => Rules::relativePath()->requiredIfAnotherFieldIs('state.installDependencies', true),
            'state.useDeployKey' => Rules::boolean(),
        ];
    }

    public function mount(): void
    {
        $this->server = $this->project->servers()->oldest()->firstOrFail();

        $this->authorize('update', $this->project);
        $this->authorize('update', $this->server);

        $this->resetState();
    }

    public function resetState(): void
    {
        $this->reset('state');

        $this->state['useDeployKey'] = $this->project->useDeployKey;

        $this->state['vcsProviderKey'] = Auth::user()->vcsProviders->first()->getKey();
    }

    public function updatedStatePackage(string $value): void
    {
        $this->state['customPackage'] = true;
    }

    public function updatedStateRepo(string $value): void
    {
        if ($this->state['customPackage'])
            return;

        if (! Str::contains($value, '/'))
            return;

        [$username, $repo] = explode('/', $value);

        $this->state['package'] = Str::lower($repo);
    }

    /**
     * Store the project's repository configuration.
     */
    public function update(InstallProjectRepoAction $installAction): void
    {
        // TODO: CRITICAL! I should command the outer Livewire "page" component to refresh after this action - it should display a completely different set of forms.

        $validated = $this->validate()['state'];

        $this->authorize('update', $this->project);
        $this->authorize('update', $this->server);

        $installAction->execute(
            $this->project,
            Auth::user()->vcsProviders()->findOrFail($validated['vcsProviderKey']),
            new ProjectRepoData(
                repo: $validated['repo'],
                branch: $validated['branch'],
                package: $validated['package'],
                use_deploy_key: $validated['useDeployKey'],
                requirements_file: $validated['requirementsFile'],
            ),
            $this->server,
            $validated['installDependencies'],
        );

        $this->resetState();

        $this->emit('project-updated');
    }

    public function render(): View
    {
        $this->vcsProviders = Auth::user()->vcsProviders()->oldest()->get();

        return view('projects.install-repo-form');
    }
}
