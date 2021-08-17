<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Deployment;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Arr;
use phpseclib3\Net\SFTP;

class RunDeploymentScriptScript extends AbstractServerScript
{
    private const PREFIX = 'MICHMAN_';

    public function execute(Server $server, Deployment $deployment, SFTP $ssh = null): void
    {
        $project = $deployment->project;

        $this->init($server, $ssh, $project->serverUsername);

        // TODO: CRITICAL! CONTINUE.

        // TODO: CRITICAL! Add some more variables here for the user's convenience and list them in docs.
        // Environment variables that will be available for the deploy script.
        $michmanVars = [
            'PROJECT_DIR' => $project->projectDir,
            'PROJECT_BRANCH' => $project->branch,
            //
        ];

        $this->exec(
            implode(' ', Arr::mapWithKeys($michmanVars,
                fn(string $name, string $value) => "{$name}=\"{$value}\""
            )) . " && source {$project->envFilePath} && cd {$project->projectDir} && {$project->deployScriptFilePath}"
        );

        if ($this->failed())
            throw new ServerScriptException('Deployment script execution failed.');

        //
    }
}
