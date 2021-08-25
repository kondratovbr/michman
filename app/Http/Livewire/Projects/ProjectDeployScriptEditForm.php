<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\ReloadProjectDeployScriptAction;
use App\Actions\Projects\UpdateProjectDeployScriptAction;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Project;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class ProjectDeployScriptEditForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInputBeforeValidation;

    public Project $project;

    public string $script = '';

    protected function prepareForValidation($attributes): array
    {
        // This will be used as a content of a file, so would be nice to make sure it ends with a newline character.
        // It won't have more than one since the attributes are trimmed before getting to this method.
        $attributes['script'] = $attributes['script'] . "\n";

        return $attributes;
    }

    public function rules(): array
    {
        return [
            'script' => Rules::string()->nullable(),
        ];
    }

    public function mount(): void
    {
        $this->authorize('update', $this->project);

        $this->resetState();
    }

    protected function resetState(): void
    {
        $this->script = $this->project->refresh()->deployScript ?? '';
    }

    /**
     * Update the project's deploy script.
     */
    public function update(UpdateProjectDeployScriptAction $action): void
    {
        $script = $this->validate()['script'] ?? '';

        $this->authorize('update', $this->project);

        $action->execute($this->project, $script);

        $this->resetState();
    }

    /**
     * Synchronously load the existing deploy script from a server (if it exists).
     */
    public function reload(ReloadProjectDeployScriptAction $action): void
    {
        $this->authorize('update', $this->project);

        $action->execute($this->project);

        $this->project->refresh();

        $this->resetState();
    }

    public function render(): View
    {
        return view('projects.project-deploy-script-edit-form');
    }
}
