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
        'installDependencies' => true,
        'useDeployKey' => null,
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

    public function rules(): array
    {
        /*
         * TODO: CRITICAL! Error messages here suck. Check it out. Check out all forms that use $state - the attribute names are all wrong like "state.repo" - should fix it.
         */

        return [
            'state.vcsProviderKey' => Rules::integer()->in(Auth::user()->vcsProviders->modelKeys())->required(),
            'state.repo' => Rules::gitRepoName()->required(),
            'state.branch' => Rules::alphaNumDashString(1, 255)->required(),
            'state.installDependencies' => Rules::boolean(),
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

    public function resetState()
    {
        $this->reset('state');

        $this->state['useDeployKey'] = $this->project->useDeployKey;

        $this->state['vcsProviderKey'] = Auth::user()->vcsProviders->first()->getKey();
    }

    /**
     * Store the project's repository configuration.
     */
    public function update(InstallProjectRepoAction $setupAction): void
    {
        // TODO: CRITICAL! I should verify the availability of the repo during the process and somehow mark it as unavailable if we can't access it the way it was setup. Probably during cloning of the repo on the server in a Script under a Job. Same with the branch we're going to use.

        // TODO: CRITICAL! I should command the outer Livewire "page" component to refresh after this action - it should display a completely different set of forms.

        $validated = $this->validate()['state'];

        $this->authorize('update', $this->project);
        $this->authorize('update', $this->server);

        $setupAction->execute(
            $this->project,
            Auth::user()->vcsProviders()->findOrFail($validated['vcsProviderKey']),
            new ProjectRepoData(
                repo: $validated['repo'],
                branch: $validated['branch'],
                use_deploy_key: $validated['useDeployKey'],
            ),
            $this->server,
            $validated['installDependencies'],
        );

        $this->reset('state');

        $this->emit('project-updated');
    }

    public function render(): View
    {
        $this->vcsProviders = Auth::user()->vcsProviders()->oldest()->get();

        return view('projects.install-repo-form');
    }
}
