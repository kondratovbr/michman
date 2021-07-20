<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Http\Livewire\Traits\ListensForEchoes;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class CreateProjectForm extends Component
{
    use AuthorizesRequests,
        TrimsInput,
        ListensForEchoes;

    public Server $server;

    /** @var string[] */
    protected $listeners = [
        //
    ];

    protected function configureEchoListeners(): void
    {
        //
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->authorize('create', [Project::class, $this->server]);
    }

    /**
     * Store a new project.
     */
    public function store(): void
    {
        //
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('projects.create-project-form');
    }
}
