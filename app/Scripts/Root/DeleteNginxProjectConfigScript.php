<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class DeleteNginxProjectConfigScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $available = "/etc/nginx/sites-available";
        $enabled = "/etc/nginx/sites-enabled";
        $file = "{$project->projectName}.conf";

        $this->exec("rm -rf {$enabled}/{$file}");
        $this->exec("rm -rf {$available}/{$file}");

        $this->exec("rm -rf {$project->userNginxConfigFilePath}");
    }
}
