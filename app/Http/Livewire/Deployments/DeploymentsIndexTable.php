<?php declare(strict_types=1);

namespace App\Http\Livewire\Deployments;

use App\Actions\Projects\DeployProjectAction;
use App\Models\Deployment;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
    use AuthorizesRequests;

    public Project $project;

    public Collection $deployments;

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

    public function render(): View
    {
        $this->deployments = $this->project->deployments()->latest()->limit(10)->get();

        return view('deployments.deployments-index-table');
    }
}
