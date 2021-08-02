<?php declare(strict_types=1);

namespace App\Http\Livewire\Deployments;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class DeploymentsIndexTable extends LivewireComponent
{
    use AuthorizesRequests;

    public Project $project;

    public Collection $deployments;

    public function mount(): void
    {
        $this->authorize('index', []);

        //
    }

    /**
     * Trigger the project's deployment.
     */
    public function deploy(): void
    {
        //
    }

    public function render(): View
    {
        $this->deployments = $this->project->deployments->latest()->limit(10)->get();

        return view('deployments.deployments-index-table');
    }
}
