<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Broadcasting\ProjectChannel;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Project;

class ProjectView extends AbstractSubpagesView
{
    use ListensForEchoes;

    protected const LAYOUT = 'layouts.app-with-menu';

    protected const VIEW = 'projects.show';

    public const VIEWS = [
        'deployment' => 'projects.deployment',
        'config' => 'projects.config',
        'queue' => 'projects.queue',
        'manage' => 'projects.manage',
        //
    ];

    /** @var string The name of a sub-page that will be shown by default. */
    protected const DEFAULT_SHOW = 'deployment';

    public Project $project;

    protected function getDefaultRoute(): string
    {
        return route('projects.show', [$this->project, static::DEFAULT_SHOW]);
    }

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ProjectChannel::name($this->project),
            [
                ProjectUpdatedEvent::class,
            ],
            '$refresh',
        );
    }

    public function canShow(string $view): bool
    {
        return (bool) match ($view) {
            'config' => $this->project->repoInstalled,
            'queue' => $this->project->deployed,
            default => true,
        };
    }
}
