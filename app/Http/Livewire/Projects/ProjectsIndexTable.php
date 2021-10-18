<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Broadcasting\ServerChannel;
use App\Events\Projects\ProjectCreatedEvent;
use App\Events\Projects\ProjectDeletedEvent;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class ProjectsIndexTable extends LivewireComponent
{
    use AuthorizesRequests;
    use ListensForEchoes;

    public Server $server;

    public Collection $projects;

    /** @var string[] */
    protected $listeners = [
        'project-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ServerChannel::name($this->server),
            [
                ProjectCreatedEvent::class,
                ProjectUpdatedEvent::class,
                ProjectDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    public function mount(): void
    {
        $this->authorize('index', [Project::class, $this->server]);
    }

    public function render(): View
    {
        $this->projects = $this->server->projects()->oldest()->get();

        return view('projects.projects-index-table');
    }
}
