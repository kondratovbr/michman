<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\ReloadProjectNginxConfigAction;
use App\Actions\Projects\UpdateProjectNginxConfigAction;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Project;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

/*
 * TODO: CRITICAL! I also forgot to implement the Gunicorn config editing features at all!
 */

class ProjectNginxConfigEditForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInputBeforeValidation;

    public Project $project;

    public string $nginxConfig = '';

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

    /**
     * Update the project's Nginx config.
     */
    public function update(UpdateProjectNginxConfigAction $action): void
    {
        $nginxConfig = $this->validate()['nginxConfig'] ?? '';

        $this->authorize('update', $this->project);

        $action->execute($this->project, $nginxConfig);

        $this->resetState();
    }

    /**
     * Synchronously load the existing Nginx config from a server (if it exists).
     */
    public function reload(ReloadProjectNginxConfigAction $action): void
    {
        $this->authorize('update', $this->project);

        $action->execute($this->project);

        $this->project->refresh();

        $this->resetState();
    }

    public function render(): View
    {
        return view('projects.project-nginx-config-edit-form');
    }
}
