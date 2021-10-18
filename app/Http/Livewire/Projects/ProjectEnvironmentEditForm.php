<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\RollbackProjectEnvironmentAction;
use App\Actions\Projects\UpdateProjectEnvironmentAction;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Project;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class ProjectEnvironmentEditForm extends LivewireComponent
{
    use AuthorizesRequests;
    use TrimsInputBeforeValidation;

    public Project $project;

    public string $environment = '';

    /** Check if the currently saved project's environment is not the same as the last deployed one. */
    public function getModifiedProperty(): bool
    {
        $deployment = $this->project->getCurrentDeployment();

        if (is_null($deployment))
            return false;

        return $deployment->environment !== $this->project->environment;
    }

    protected function prepareForValidation($attributes): array
    {
        // This will be used as a content of a file, so would be nice to make sure it ends with a newline character.
        // It won't have more than one since the attributes are trimmed before getting to this method.
        $attributes['environment'] = $attributes['environment'] . "\n";

        return $attributes;
    }

    public function rules(): array
    {
        return [
            'environment' => Rules::string()->nullable(),
        ];
    }

    public function mount(): void
    {
        $this->authorize('update', $this->project);

        $this->resetState();
    }

    protected function resetState(): void
    {
        $this->environment = $this->project->refresh()->environment ?? '';
    }

    /** Update the project's environment. */
    public function update(UpdateProjectEnvironmentAction $action): void
    {
        $environment = $this->validate()['environment'] ?? null;

        $this->authorize('update', $this->project);

        $action->execute($this->project, $environment);

        $this->resetState();
    }

    /** Replace the current project's environment with the last deployed one. */
    public function rollback(RollbackProjectEnvironmentAction $action): void
    {
        $this->authorize('update', $this->project);

        $action->execute($this->project);

        $this->resetState();
    }

    public function render(): View
    {
        return view('projects.project-environment-edit-form');
    }
}
