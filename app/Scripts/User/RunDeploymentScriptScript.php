<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Deployment;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Support\Arr;
use phpseclib3\Net\SFTP;

class RunDeploymentScriptScript extends AbstractServerScript
{
    private const PREFIX = 'MICHMAN_';

    public function execute(Server $server, Deployment $deployment, SFTP $ssh = null): void
    {
        $project = $deployment->project;

        $this->init($server, $ssh, $project->serverUsername);

        // TODO: Maybe retrieve commit message and add it here. May be useful for some. Git tags?
        // Environment variables that will be available for the deploy script.
        $michmanVars = [
            'PROJECT_ROOT' => $project->projectDir,
            'BRANCH' => $project->branch,
            'DOMAIN' => $project->domain,
            'MANUAL' => $deployment->isManual(),
            'AUTO' => $deployment->isAutomatic(),
            'COMMIT' => $deployment->commit,
            'USER' => $project->serverUsername,
        ];

        $this->exec(
            implode(' ', Arr::mapWithKeys($michmanVars,
                fn(string $name, $value) => static::PREFIX . $name . "=\"{$value}\""
            )) . " && source {$project->envFilePath} && cd {$project->projectDir} && {$project->deployScriptFilePath}"
        );
    }
}
