<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;

class DeleteProjectAction
{
    public function __construct(
        UninstallProjectRepoAction $uninstallRepo,
    ) {}

    public function execute(Project $project): void
    {
        // TODO: CRITICAL! CONTINUE. Implement.

        //
    }
}
