<?php declare(strict_types=1);

namespace App\Http\Livewire\Workers;

use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class WorkersIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Project $project;

    public Collection $workers;

    /** @var string[] */
    protected $listeners = [
        //
    ];

    protected function configureEchoListeners(): void
    {
        //
    }

    public function mount(): void
    {
        $this->authorize('index', [Worker::class, $this->project]);
    }

    public function render(): View
    {
        $this->workers = $this->project->workers()->oldest()->get();

        return view('workers.workers-index-table');
    }
}
