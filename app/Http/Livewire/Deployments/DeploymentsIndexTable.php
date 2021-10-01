<?php declare(strict_types=1);

namespace App\Http\Livewire\Deployments;

use App\Actions\Projects\DeployProjectAction;
use App\Broadcasting\ProjectChannel;
use App\Events\Deployments\DeploymentCreatedEvent;
use App\Events\Deployments\DeploymentUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Deployment;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class DeploymentsIndexTable extends LivewireComponent
{
    use AuthorizesRequests;
    use ListensForEchoes;

    public Project $project;

    public Collection $deployments;

    /** Indicates if a confirmation modal should currently be opened. */
    public bool $modalOpen = false;
    /** Logs to show in the logs view modal. */
    public Collection|null $logs = null;
    /** The server that the logs that are currently shown are taken from. */
    public Server $server;

    /** @var string[] */
    protected $listeners = [
        //
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ProjectChannel::name($this->project),
            [
                DeploymentCreatedEvent::class,
                DeploymentUpdatedEvent::class,
            ],
            '$refresh',
        );
    }

    public function mount(): void
    {
        $this->authorize('index', [Deployment::class, $this->project]);
    }

    /** Trigger the project's deployment. */
    public function deploy(DeployProjectAction $deployAction): void
    {
        $this->authorize('create', [Deployment::class, $this->project]);

        $deployAction->execute($this->project);
    }

    /** Open a modal with a deployment output log. */
    public function showLog(string $deploymentKey, string $serverKey): void
    {
        $deployment = Deployment::validated($deploymentKey, $this->deployments);

        $this->authorize('view', $deployment);

        $this->server = $deployment->servers()->findOrFail($serverKey);

        $this->logs = $this->server->serverDeployment->logs();

        $this->modalOpen = true;
    }

    public function render(): View
    {
        $this->deployments = $this->project->deployments()->latest()->limit(10)->get();

        return view('deployments.deployments-index-table');
    }
}
