<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Actions\Databases\StoreDatabaseAction;
use App\Actions\DatabaseUsers\StoreDatabaseUserAction;
use App\Actions\DeploySshKeys\CreateDeploySshKeyAction;
use App\Actions\Pythons\StorePythonAction;
use App\DataTransferObjects\DatabaseUserDto;
use App\DataTransferObjects\NewProjectDto;
use App\Jobs\Servers\CreateUserOnServerJob;
use App\Models\Project;
use App\Models\Server;

class StoreProjectAction
{
    public function __construct(
        protected CreateDeploySshKeyAction $createDeploySshKey,
        protected StorePythonAction $storePython,
        protected StoreDatabaseAction $storeDatabase,
        protected StoreDatabaseUserAction $storeDatabaseUser,
    ) {}

    public function execute(NewProjectDto $data, Server $server): Project
    {
        $user = $server->user;

        /** @var Project $project */
        $project = $user->projects()->create($data->toArray());

        $server->projects()->attach($project);

        $this->createDeploySshKey->execute($project);

        if ($server->pythons()->where('version', $data->python_version)->count() < 1) {
            $this->storePython->execute($data->python_version, $server);
        }

        if (
            $data->create_database
            && $server->databases()->where('name', $data->db_name)->count() < 1
        ) {
            $database = $this->storeDatabase->execute($data->db_name, $server);

            $project->database()->associate($database);

            if (
                $data->create_db_user
                && $server->databaseUsers()->where('name', $data->db_user_name)->count() < 1
            ) {
                $databaseUser = $this->storeDatabaseUser->execute(new DatabaseUserDto(
                    name: $data->db_user_name,
                    password: $data->db_user_password,
                ), $server, collection([$database]));

                $project->databaseUser()->associate($databaseUser);
            }
        }

        $project->save();

        CreateUserOnServerJob::dispatch($server, $project->serverUsername);

        return $project;
    }
}
