<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Facades\Auth;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! CONTINUE.

class ConfigureRepoForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInput,
        ListensForEchoes;

    public Project $project;

    public Collection $vcsProviders;

    public array $state = [
        'vcsProviderKey' => null,
        'repo' => '',
        'branch' => '',
        'installDependencies' => true,
        'useDeployKey' => null,
    ];

    /** @var string[] */
    protected $listeners = [
        //
    ];

    protected function configureEchoListeners(): void
    {
        // TODO: Implement configureEchoListeners() method.
    }

    public function mount(): void
    {
        $this->authorize('update', $this->project);

        $this->resetState();
    }

    public function resetState()
    {
        $this->reset('state');

        $this->state['useDeployKey'] = $this->project->useDeployKey;
    }

    public function render(): View
    {
        $this->vcsProviders = Auth::user()->vcsProviders()->oldest()->get();

        return view('projects.configure-repo-form');
    }
}
