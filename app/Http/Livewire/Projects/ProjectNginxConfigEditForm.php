<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\RollbackProjectNginxConfigAction;
use App\Actions\Projects\UpdateProjectNginxConfigAction;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Project;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class ProjectNginxConfigEditForm extends LivewireComponent
{
    use AuthorizesRequests;
    use TrimsInputBeforeValidation;

    public Project $project;

    public string $nginxConfig = '';

    /** Check if the currently saved project's Nginx config is not the same as the last deployed one. */
    public function getModifiedProperty(): bool
    {
        $deployment = $this->project->getCurrentDeployment();

        if (is_null($deployment))
            return false;

        return $deployment->nginxConfig !== $this->project->nginxConfig;
    }

    protected function prepareForValidation($attributes): array
    {
        // This will be used as a content of a file, so would be nice to make sure it ends with a newline character.
        // It won't have more than one since the attributes are trimmed before getting to this method.
        $attributes['nginxConfig'] = $attributes['nginxConfig'] . "\n";

        return $attributes;
    }

    public function rules(): array
    {
        return [
            'nginxConfig' => Rules::string()->nullable(),
        ];
    }

    public function mount(): void
    {
        $this->authorize('update', $this->project);

        $this->resetState();
    }

    protected function resetState(): void
    {
        $this->nginxConfig = $this->project->refresh()->nginxConfig ?? '';
    }

    /** Update the project's Nginx config. */
    public function update(UpdateProjectNginxConfigAction $action): void
    {
        $nginxConfig = $this->validate()['nginxConfig'] ?? '';

        $this->authorize('update', $this->project);

        $action->execute($this->project, $nginxConfig);

        $this->resetState();
    }

    /** Replace the current project's Nginx config with the last deployed one. */
    public function rollback(RollbackProjectNginxConfigAction $action): void
    {
        $this->authorize('update', $this->project);

        $action->execute($this->project);

        $this->resetState();
    }

    public function render(): View
    {
        return view('projects.project-nginx-config-edit-form');
    }
}
