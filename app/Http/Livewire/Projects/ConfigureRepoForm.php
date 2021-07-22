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

    public int|null $vcsProviderKey = null;
    public string $repo = '';
    public string $branch = '';
    public bool $installDependencies = true;
    public bool $useDeployKey = false;

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
    }

    public function render(): View
    {
        $this->vcsProviders = Auth::user()->vcsProviders()->oldest()->get();

        return view('projects.configure-repo-form');
    }
}
