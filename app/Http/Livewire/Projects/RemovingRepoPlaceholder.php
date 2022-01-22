<?php declare(strict_types=1);

namespace App\Http\Livewire\Projects;

use App\Broadcasting\ProjectChannel;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;

class RemovingRepoPlaceholder extends LivewireComponent
{
    use ListensForEchoes;

    public Project $project;

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ProjectChannel::name($this->project),
            [
                ProjectUpdatedEvent::class,
            ],
            'projectUpdated',
        );
    }

    public function projectUpdated(): void
    {
        $this->emitUp('refresh-view');
    }

    public function render(): View
    {
        return view('projects.removing-repo-placeholder');
    }
}
