<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\InstallProjectRepoAction;
use App\Broadcasting\UserChannel;
use App\DataTransferObjects\ProjectRepoDto;
use App\Events\VcsProviders\VcsProviderCreatedEvent;
use App\Events\VcsProviders\VcsProviderDeletedEvent;
use App\Events\VcsProviders\VcsProviderUpdatedEvent;
use App\Facades\Auth;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Project;
use App\Models\Server;
use App\Models\VcsProvider;
use App\Support\Str;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: IMPORTANT! Cover with tests.

// TODO: VERY IMPORTANT! This process shouldn't start until the project creation is completed.

/**
 * @property-read VcsProvider|null $vcsProvider
 */
class InstallRepoForm extends LivewireComponent
{
    use AuthorizesRequests;
    use TrimsInputBeforeValidation;
    use ListensForEchoes;

    public Server $server;
    public Project $project;

    public Collection $vcsProviders;

    public array $state = [
        'vcsProviderKey' => null,
        'repo' => '',
        'branch' => 'main',
        'package' => '',
        'root' => 'static',
        'installDependencies' => true,
        'useDeployKey' => true,
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
            UserChannel::name(user()),
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
        return [
            'state.vcsProviderKey' => Rules::integer()->in(user()->vcsProviders->modelKeys())->required(),
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

    public function getVcsProviderProperty(): VcsProvider|null
    {
        $vcsProviderKey = $this->validateOnly('state.vcsProviderKey')['state']['vcsProviderKey'];

        /** @var VcsProvider|null $vcs */
        $vcs = user()->vcsProviders()->find($vcsProviderKey);

        return $vcs;
    }

    public function resetState(): void
    {
        $this->reset('state');

        $this->state['vcsProviderKey'] = user()->vcsProviders->first()->getKey();
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

        $repo = explode('/', $value)[1];

        $this->state['package'] = Str::lower($repo);
    }

    /** Check if deploy key must be used with the currently chosen provider. */
    public function mustUseDeployKey(): bool
    {
        return $this->vcsProvider->mustUseDeployKey();
    }

    /** Store the project's repository configuration. */
    public function update(InstallProjectRepoAction $installAction): void
    {
        $state = $this->validate()['state'];

        $this->authorize('update', $this->project);
        $this->authorize('update', $this->server);

        $installAction->execute(
            $this->project,
            user()->vcsProviders()->findOrFail($state['vcsProviderKey']),
            new ProjectRepoDto(
                root: $state['root'],
                repo: $state['repo'],
                branch: $state['branch'],
                package: $state['package'],
                use_deploy_key: $this->mustUseDeployKey() ? true : $state['useDeployKey'],
                requirements_file: $state['requirementsFile'],
            ),
            $this->server,
            $state['installDependencies'],
        );

        $this->resetState();

        $this->emit('project-updated');
        $this->emitUp('refresh-view');
    }

    public function render(): View
    {
        $this->vcsProviders = user()->vcsProviders()->oldest()->get();

        return view('projects.install-repo-form');
    }
}
