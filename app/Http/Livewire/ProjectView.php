<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Project;

class ProjectView extends AbstractSubpagesView
{
    protected const LAYOUT = 'layouts.app-with-menu';

    protected const VIEW = 'projects.show';

    public const VIEWS = [
        'repo' => 'projects.repo',
        //
    ];

    /** @var string The name of a sub-page that will be shown by default. */
    protected const DEFAULT_SHOW = 'repo';

    public Project $project;
}
