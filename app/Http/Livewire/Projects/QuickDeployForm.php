<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Actions\Webhooks\CreateProjectWebhookAction;
use App\Actions\Webhooks\DeleteProjectWebhookAction;
use App\Broadcasting\ProjectChannel;
use App\Events\Webhooks\WebhookCreatedEvent;
use App\Events\Webhooks\WebhookDeletedEvent;
use App\Events\Webhooks\WebhookUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Project;
use App\Models\Webhook;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

/**
 * @property-read Webhook|null $hook
 */
class QuickDeployForm extends LivewireComponent
{
    use AuthorizesRequests;
    use ListensForEchoes;

    public Project $project;

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ProjectChannel::name($this->project),
            [
                WebhookCreatedEvent::class,
                WebhookUpdatedEvent::class,
                WebhookDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    public function mount(): void
    {
        $this->authorize('view', $this->project);
    }

    public function getHookProperty(): Webhook|null
    {
        /** @var Webhook|null $hook */
        $hook = $this->project->webhook()->first();
        return $hook;
    }

    /** Enable automatic deployment for this project. */
    public function enable(CreateProjectWebhookAction $action): void
    {
        $this->authorize('create', [Webhook::class, $this->project]);

        if ($this->project->webhookEnabled)
            return;

        $action->execute($this->project);
    }

    /** Disable automatic deployment for this project. */
    public function disable(DeleteProjectWebhookAction $action): void
    {
        if (! isset($this->project->webhook))
            return;

        $this->authorize('delete', $this->project->webhook);

        $action->execute($this->project->webhook);
    }

    public function render(): View
    {
        return view('projects.quick-deploy-form');
    }
}
