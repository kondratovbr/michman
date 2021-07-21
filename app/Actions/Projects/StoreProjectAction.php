<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\DataTransferObjects\NewProjectData;
use App\Jobs\Databases\CreateDatabaseJob;
use App\Jobs\Pythons\CreatePythonJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class StoreProjectAction
{
    public function execute(NewProjectData $data, Server $server): Project
    {
        return DB::transaction(function () use ($data, $server) {
            /** @var Server $server */
            $server = Server::query()->lockForUpdate()->findOrFail($server->getKey());

            $server->projects()->create($data->toArray());

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
        }, 5);
    }
}
