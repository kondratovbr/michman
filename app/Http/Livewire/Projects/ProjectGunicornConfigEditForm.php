<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\ReloadProjectGunicornConfigAction;
use App\Actions\Projects\UpdateProjectGunicornConfigAction;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Project;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class ProjectGunicornConfigEditForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInputBeforeValidation;

    public Project $project;

    public string $gunicornConfig;

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

    /**
     * Update the project's Gunicorn config.
     */
    public function update(UpdateProjectGunicornConfigAction $action): void
    {
        $gunicornConfig = $this->validate()['gunicornConfig'] ?? '';

        $this->authorize('update', $this->project);

        $action->execute($this->project, $gunicornConfig);

        $this->resetState();
    }

    /**
     * Synchronously load the existing Gunicorn config from a server (if it exists).
     */
    public function reload(ReloadProjectGunicornConfigAction $action): void
    {
        $this->authorize('update', $this->project);

        $action->execute($this->project);

        $this->project->refresh();

        $this->resetState();
    }

    public function render(): View
    {
        return view('projects.project-gunicorn-config-edit-form');
    }
}
