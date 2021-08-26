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

// TODO: CRITICAL! Test if all of this works. I have some complex queries going on with finished/successful deployments and such.

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
// TODO: CRITICAL! Obviously - don't forget to change the deployment logic accordingly.

class ProjectEnvironmentEditForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInputBeforeValidation;

    public Project $project;

    public string $environment = '';

    /**
     * Check if the currently saved project's environment is not the same as the last deployed one.
     */
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
     * Replace the current project's environment with the last deployed one.
     */
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
