<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\DeleteProjectAction;
use App\Models\Project;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class DeleteProjectForm extends LivewireComponent
{
    use AuthorizesRequests;

    public Project $project;

    public bool $confirmationModalOpen = false;
    public string $projectName = '';

    public function rules(): array
    {
        return [
            'projectName' => Rules::string(1, 255)
                // TODO: The error message here comes out cryptic because this rule is intended for selects. Fix.
                ->in([$this->project->domain])
                ->required(),
        ];
    }

    public function openConfirmationModal(): void
    {
        $this->resetErrorBag();

        $this->dispatchBrowserEvent('confirmation-modal-opened');

        $this->confirmationModalOpen = true;
    }

    /** Delete the project. */
    public function delete(DeleteProjectAction $delete): void
    {
        $this->authorize('delete', [$this->project]);

        $this->validate();

        $delete->execute($this->project);

        $this->redirectRoute('home');
    }

    public function render(): View
    {
        return view('projects.delete-project-form');
    }
}
