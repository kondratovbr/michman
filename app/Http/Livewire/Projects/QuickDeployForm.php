<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Projects\DisableWebhookAction;
use App\Actions\Projects\EnableWebhookAction;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class QuickDeployForm extends LivewireComponent
{
    use AuthorizesRequests;

    public Project $project;

    public function mount(): void
    {
        $this->authorize('update', $this->project);
    }

    /** Enable automatic deployment for this project. */
    public function enable(EnableWebhookAction $action): void
    {
        $this->authorize('update', $this->project);

        if ($this->project->webhookEnabled)
            return;

        $action->execute($this->project);
    }

    /** Disable automatic deployment for this project. */
    public function disable(DisableWebhookAction $action): void
    {
        $this->authorize('update', $this->project);

        if (! $this->project->webhookEnabled)
            return;

        $action->execute($this->project);
    }

    public function render(): View
    {
        return view('projects.quick-deploy-form');
    }
}
