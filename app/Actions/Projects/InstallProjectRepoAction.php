<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\DataTransferObjects\ProjectRepoDto;
use App\Facades\ConfigView;
use App\Jobs\DeploySshKeys\UploadDeploySshKeyToServerJob;
use App\Jobs\Projects\InstallProjectToServerJob;
use App\Jobs\ServerSshKeys\AddServerSshKeyToVcsJob;
use App\Jobs\ServerSshKeys\UploadServerSshKeyToServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\VcsProvider;
use App\Support\Str;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class InstallProjectRepoAction
{
    public function execute(
        Project $project,
        VcsProvider $vcsProvider,
        ProjectRepoDto $data,
        Server $server,
        bool $installDependencies,
    ): Project {
        return DB::transaction(function () use (
            $project, $vcsProvider, $data, $server, $installDependencies
        ): Project {
            $project = $project->freshLockForUpdate();

            $project->vcsProvider()->associate($vcsProvider);
            $project->fill($data->toArray());

            $project->environment = ConfigView::render('default_env_file', $this->getEnvData($project, $server));
            $project->deployScript = ConfigView::render('default_deploy_script', ['project' => $project]);
            $project->gunicornConfig = ConfigView::render('gunicorn.default_config', ['project' => $project]);
            $project->nginxConfig = ConfigView::render('nginx.project', ['project' => $project]);
            $project->save();

            $jobs = [];

            if ($project->useDeployKey) {
                $jobs[] = new UploadDeploySshKeyToServerJob($server, $project);
            } else {
                $jobs[] = new UploadServerSshKeyToServerJob($server, $project->serverUsername);
                $jobs[] = new AddServerSshKeyToVcsJob($server, $vcsProvider);
            }

            $jobs[] = new InstallProjectToServerJob($project, $server, $installDependencies);

            Bus::chain($jobs)->dispatch();

            return $project;
        }, 5);
    }

    /** Create an array with the project's environment data that can be supplied to config templates. */
    protected function getEnvData(Project $project, Server $server): array
    {
        $envData = [
            'project' => $project,
            'secretKey' => Str::random(50),
        ];

        if (! empty($server->installedDatabase)) {
            $envData['databaseUrlPrefix'] = (string) config("servers.databases.{$server->installedDatabase}.django_url_prefix");
            // TODO: IMPORTANT! This only works for "app" server type. Handle other types as well.
            $envData['databaseHost'] = '127.0.0.1';
            $envData['databasePort'] = (string) config("servers.databases.{$server->installedDatabase}.default_port");
            if (isset($project->database)) {
                $envData['databaseName'] = $project->database->name;
            }
            if (isset($project->databaseUser)) {
                $envData['databaseUser'] = $project->databaseUser->name;
                $envData['databasePassword'] = $project->databaseUser->password;
            }
            if (isset($project->database) && isset($project->databaseUser)) {
                $envData['databaseUrl'] = "{$envData['databaseUrlPrefix']}://{$project->databaseUser->name}:{$project->databaseUser->password}@{$envData['databaseHost']}:{$envData['databasePort']}/{$project->database->name}";
                $envData['databaseName'] = $project->database->name;
            }
        }

        if (! empty($server->installedCache)) {
            $envData['cacheUrlPrefix'] = (string) config("servers.caches.{$server->installedCache}.django_url_prefix");
            $envData['cacheHost'] = '127.0.0.1';
            $envData['cachePort'] = (string) config("servers.caches.{$server->installedCache}.default_port");
            $envData['cacheUrl'] = "{$envData['cacheUrlPrefix']}://{$envData['cacheHost']}:{$envData['cachePort']}";
        }

        return $envData;
    }
}
