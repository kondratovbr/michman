<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\UpdateProjectDeploymentBranchAction;
use App\Models\Project;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class ProjectDeploymentBranchEditForm extends LivewireComponent
{
    use AuthorizesRequests;

    public Project $project;

    public string $branch;

    public function rules(): array
    {
        return [
            'branch' => Rules::alphaNumDashString(1, 255)->required(),
        ];
    }

    public function mount(): void
    {
        $this->authorize('update', $this->project);

        $this->resetState();
    }

    protected function resetState(): void
    {
        $this->branch = $this->project->refresh()->branch;
    }

    /** Save the new deployment branch. */
    public function update(UpdateProjectDeploymentBranchAction $action): void
    {
        $branch = $this->validate()['branch'];

        $this->authorize('update', $this->project);

        $action->execute($this->project, $branch);

        $this->resetState();

        $this->emit('project-updated');
    }

    public function render(): View
    {
        return view('projects.project-deployment-branch-edit-form');
    }
}
