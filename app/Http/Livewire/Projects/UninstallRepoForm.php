<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\UninstallProjectRepoAction;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

/*
 * TODO: CRITICAL! DELETING. Make sure no actions can be done with the project when we removing the repo,
 *       and make sure the UI cannot be used.
 */

class UninstallRepoForm extends LivewireComponent
{
    use AuthorizesRequests;

    public Project $project;

    public function mount(): void
    {
        $this->authorize('update', $this->project);
    }

    public function uninstall(UninstallProjectRepoAction $action): void
    {
        $this->authorize('update', $this->project);

        $action->execute($this->project);
    }

    public function render(): View
    {
        return view('projects.uninstall-repo-form');
    }
}
