<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Actions\Databases\StoreDatabaseAction;
use App\Actions\DeploySshKeys\CreateDeploySshKeyAction;
use App\Actions\Pythons\StorePythonAction;
use App\DataTransferObjects\DatabaseData;
use App\DataTransferObjects\NewProjectData;
use App\DataTransferObjects\PythonData;
use App\Models\Project;
use App\Models\Server;
use App\Models\User;

// TODO: CRITICAL! Cover with tests.

// TODO: CRITICAL! Refactor my dumb "sync" actions - don't run those stupid "create" jobs that don't interact with any external services - just chain actions fot it! And don't forget to update tests.

class StoreProjectAction
{
    public function __construct(
        protected StorePythonAction $storePython,
        protected StoreDatabaseAction $storeDatabase,
        protected CreateDeploySshKeyAction $createDeploySshKey,
    ) {}

    public function execute(NewProjectData $data, User $user, Server $server): Project
    {
        /** @var Project $project */
        $project = $user->projects()->create($data->toArray());

        $server->projects()->attach($project);

        $this->createDeploySshKey->execute($project);

        if ($server->pythons()->where('version', $data->python_version)->count() < 1) {
            $this->storePython->execute(new PythonData(
                version: $data->python_version,
            ), $server);
        }

        if (
            $data->create_database
            && $server->databases()->where('name', $data->db_name)->count() < 1
        ) {
            $this->storeDatabase->execute(new DatabaseData(
                name: $data->db_name,
            ), $server);
        }

        return $project;
    }
}
