<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Facades\ConfigView;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class UploadProjectConfigFilesToServerScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh, $project->serverUsername);

        $username = $project->serverUsername;
        $projectName = $project->projectName;
        $domain = $project->domain;
        $michmanDir = "/home/{$username}/.michman";
        $projectDir = "/home/{$username}/{$domain}";

        $deployScriptFile = "{$michmanDir}/{$projectName}_deploy.sh";
        $envFile = "{$projectDir}/.env";

        if (! $this->sendString(
            $deployScriptFile,
            ConfigView::render('default_deploy_script', ['project' => $project]),
        )) {
            throw new \RuntimeException("Failed to send string to file: {$deployScriptFile}");
        }

        if (! $this->sendString(
            $envFile,
            ConfigView::render('default_env_file', ['project' => $project]),
        )) {
            throw new \RuntimeException("Failed to send string to file: {$envFile}");
        }
    }
}
