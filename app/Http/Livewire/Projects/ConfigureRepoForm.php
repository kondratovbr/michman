<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Http\Livewire\Traits\ListensForEchoes;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! CONTINUE.

class ConfigureRepoForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInput,
        ListensForEchoes;

    public Project $project;

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
        return view('projects.configure-repo-form');
    }
}
