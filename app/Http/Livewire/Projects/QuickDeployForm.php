<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Webhooks\CreateProjectWebhookAction;
use App\Actions\Webhooks\DeleteProjectWebhookAction;
use App\Models\Project;
use App\Models\Webhook;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class QuickDeployForm extends LivewireComponent
{
    use AuthorizesRequests;

    public Project $project;
    public Webhook|null $hook;

    public function mount(): void
    {
        $this->authorize('update', $this->project);
    }

    /** Enable automatic deployment for this project. */
    public function enable(CreateProjectWebhookAction $action): void
    {
        $this->authorize('update', $this->project);

        if ($this->project->webhookEnabled)
            return;

        $action->execute($this->project);
    }

    /** Disable automatic deployment for this project. */
    public function disable(DeleteProjectWebhookAction $action): void
    {
        $this->authorize('update', $this->project);

        if (! isset($this->project->webhook))
            return;

        $action->execute($this->project->webhook);
    }

    public function render(): View
    {
        $this->hook = $this->project->webhook()->first();

        return view('projects.quick-deploy-form');
    }
}
