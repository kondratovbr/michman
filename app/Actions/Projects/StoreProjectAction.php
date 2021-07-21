<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\DataTransferObjects\NewProjectData;
use App\Jobs\Databases\CreateDatabaseJob;
use App\Jobs\Pythons\CreatePythonJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

// TODO: CRITICAL! Cover with tests.

class StoreProjectAction
{
    public function execute(NewProjectData $data, User $user, Server $server): Project
    {
        /** @var Project $project */
        $project = $user->projects()->create($data->toArray());

        $server->projects()->attach($project);

        $jobs = [];

        if ($server->pythons()->where('version', $data->python_version)->count() < 1) {
            $jobs[] = new CreatePythonJob($server, $data->python_version, true);
        }

        if (
            $data->create_database
            && $server->databases()->where('name', $data->db_name)->count() < 1
        ) {
            $jobs[] = new CreateDatabaseJob($server, $data->db_name, true);
        }

        Bus::chain($jobs)->dispatch();

        return $project;
    }
}
