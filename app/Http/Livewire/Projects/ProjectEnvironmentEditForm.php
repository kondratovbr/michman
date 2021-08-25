<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\ReloadProjectEnvironmentAction;
use App\Actions\Projects\UpdateProjectEnvironmentAction;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Project;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

/*
 * TODO: CRITICAL! I need to change the logic of these config editing forms slightly - don't update actual files on servers immediately. Upload the updated files only during the next deployment, or else it would be very easy to break something and hard to track it.
 *     This would mean that I have to make the logic of these forms more complicated:
 *         - Notify the user that the file won't be updated immediately.
 *         - Notify the user if servers currently have a different version of the config.
 *         - Allow the user to rollback the changes back to the currently deployed version.
 *         - ...
 *     NOTE: Currently I don't even restart the corresponding services when updating the configs,
 *           which is just stupid - they may restart automatically, so I can't rely on it.
 */

class ProjectEnvironmentEditForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInputBeforeValidation;

    public Project $project;

    public string $environment = '';

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

    /**
     * Update the project's environment.
     */
    public function update(UpdateProjectEnvironmentAction $action): void
    {
        /*
         * TODO: CRITICAL! Go through all the rest of my Livewire forms - I'm pretty sure I've been stupid
         *       and used the raw attributes instead of the validated ones on some occasions.
         */

        $environment = $this->validate()['environment'] ?? null;

        $this->authorize('update', $this->project);

        $action->execute($this->project, $environment);

        $this->resetState();
    }

    /**
     * Synchronously load the existing environment from a server (if it exists).
     */
    public function reload(ReloadProjectEnvironmentAction $action): void
    {
        $this->authorize('update', $this->project);

        $action->execute($this->project);

        $this->project->refresh();

        $this->resetState();
    }

    public function render(): View
    {
        return view('projects.project-environment-edit-form');
    }
}
