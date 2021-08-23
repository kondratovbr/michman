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
use App\Models\ServerLog;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Livewire\Component as LivewireComponent;

/*
 * TODO: CRITICAL! Don't forget to implement something like "Quick Deploy" in Forge.
 *       Should figure out how it's done.
 *       Probably by installing some GitHub hook or something using their API,
 *       so that it makes some request to us when a commit is done to the deployment branch.
 *       I've seen that GitHub can "notify" apps on its "events" happening.
 */

class DeploymentsIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

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
        /*
         * TODO: CRITICAL! Check if this works.
         *       Deployment process only updates pivots, check if timestamps on Deployment and Server
         *       models get updated and if these events get triggered.
         */
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

    /**
     * Trigger the project's deployment.
     */
    public function deploy(DeployProjectAction $deployAction): void
    {
        $this->authorize('deploy', $this->project);

        $deployAction->execute($this->project);
    }

    /**
     * Open a modal with a deployment output log.
     */
    public function showLog(string $deploymentKey, string $serverKey): void
    {
        $deploymentKey = Validator::make(
            ['deployment_key' => $deploymentKey,],
            ['deployment_key' => Rules::string(1, 16)
                ->in($this->deployments->modelKeys())
                ->required()],
        )->validate()['deployment_key'];

        /** @var Deployment $deployment */
        $deployment = $this->project->deployments()->findOrFail($deploymentKey);

        $this->authorize('view', $deployment);

        $serverKey = Validator::make(
            ['server_key' => $serverKey,],
            ['server_key' => Rules::string(1, 16)
                ->in($deployment->servers->modelKeys())
                ->required()],
        )->validate()['server_key'];

        $this->server = $deployment->servers()->findOrFail($serverKey);
        $pivot = $this->server->serverDeployment;

        $this->logs = ServerLog::query()
            ->where('server_id', $this->server->getKey())
            ->whereBetween('created_at', [$pivot->startedAt, $pivot->finishedAt])
            ->oldest()
            ->get();

        $this->modalOpen = true;
    }

    public function render(): View
    {
        $this->deployments = $this->project->deployments()->latest()->limit(10)->get();

        return view('deployments.deployments-index-table');
    }
}
