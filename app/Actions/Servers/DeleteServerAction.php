<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\Actions\Projects\DeleteProjectAction;
use App\Jobs\Servers\DeleteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\States\Servers\Deleting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: Cover with tests.

class DeleteServerAction
{
    public function __construct(
        private DeleteProjectAction $deleteProject,
    ) {}

    public function execute(Server $server): void
    {
        DB::transaction(function () use ($server) {
            $server = $server->freshLockForUpdate();

            if (! $server->state->canTransitionTo(Deleting::class))
                return;

            $server->state->transitionTo(Deleting::class);

            $jobs = new Collection;

            /** @var Project $project */
            foreach ($server->projects as $project) {
                if ($project->servers->count() === 1)
                    $jobs = $jobs->concat($this->deleteProject->execute($project, true));
            }

            $jobs->push(new DeleteServerJob($server));

            Bus::chain($jobs->toArray())->dispatch();
        }, 5);
    }
}
