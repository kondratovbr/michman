<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Http\Livewire\Traits\ListensForEchoes;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Project;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

/*
 * TODO: CRITICAL! CONTINUE. https://devdojo.com/tnylea/using-ace-editor-with-livewire
 */

class ProjectEnvironmentEditForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInput,
        ListensForEchoes;

    public Project $project;

    public string $content = '';

    /** @var string[] */
    protected $listeners = [
        //
    ];

    protected function configureEchoListeners(): void
    {
        //
    }

    protected function prepareForValidation($attributes): array
    {
        $attributes['content'] = trim($attributes['content']);

        return $attributes;
    }

    public function rules(): array
    {
        return [
            'content' => Rules::string()->required(),
        ];
    }

    public function mount(): void
    {
        $this->authorize('update', $this->project);

        $this->content = $this->project->environment ?? '';
    }

    /**
     * Update the project's environment.
     */
    public function update(): void
    {
        $content = $this->validate()['content'];

        $this->authorize('update', $this->project);

        //
    }

    public function render(): View
    {
        return view('projects.project-environment-edit-form');
    }
}
