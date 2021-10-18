<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\RollbackProjectGunicornConfigAction;
use App\Actions\Projects\UpdateProjectGunicornConfigAction;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Project;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class ProjectGunicornConfigEditForm extends LivewireComponent
{
    use AuthorizesRequests;
    use TrimsInputBeforeValidation;

    public Project $project;

    public string $gunicornConfig;

    /** Check if the currently saved project's Gunicorn config is not the same as the last deployed one. */
    public function getModifiedProperty(): bool
    {
        $deployment = $this->project->getCurrentDeployment();

        if (is_null($deployment))
            return false;

        return $deployment->gunicornConfig !== $this->project->gunicornConfig;
    }

    protected function prepareForValidation($attributes): array
    {
        // This will be used as a content of a file, so would be nice to make sure it ends with a newline character.
        // It won't have more than one since the attributes are trimmed before getting to this method.
        $attributes['gunicornConfig'] = $attributes['gunicornConfig'] . "\n";

        return $attributes;
    }

    public function rules(): array
    {
        return [
            'gunicornConfig' => Rules::string()->nullable(),
        ];
    }

    public function mount(): void
    {
        $this->authorize('update', $this->project);

        $this->resetState();
    }

    protected function resetState(): void
    {
        $this->gunicornConfig = $this->project->refresh()->gunicornConfig ?? '';
    }

    /** Update the project's Gunicorn config. */
    public function update(UpdateProjectGunicornConfigAction $action): void
    {
        $gunicornConfig = $this->validate()['gunicornConfig'] ?? '';

        $this->authorize('update', $this->project);

        $action->execute($this->project, $gunicornConfig);

        $this->resetState();
    }

    /** Replace the current project's Gunicorn config with the last deployed one. */
    public function rollback(RollbackProjectGunicornConfigAction $action): void
    {
        $this->authorize('update', $this->project);

        $action->execute($this->project);

        $this->resetState();
    }

    public function render(): View
    {
        return view('projects.project-gunicorn-config-edit-form');
    }
}
