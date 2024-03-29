<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\UninstallProjectRepoAction;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: VERY IMPORTANT! Add a confirmation modal here. On other destructive actions as well.

class UninstallRepoForm extends LivewireComponent
{
    use AuthorizesRequests;

    public Project $project;

    public function mount(): void
    {
        $this->authorize('view', $this->project);
    }

    public function uninstall(UninstallProjectRepoAction $action): void
    {
        $this->authorize('update', $this->project);

        $action->execute($this->project);

        $this->emitUp('refresh-view');
    }

    public function render(): View
    {
        return view('projects.uninstall-repo-form');
    }
}
