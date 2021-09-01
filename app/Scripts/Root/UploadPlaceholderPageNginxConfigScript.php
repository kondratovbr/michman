<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Facades\ConfigView;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class UploadPlaceholderPageNginxConfigScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        // TODO: CRITICAL! Use mkdir here to create those two nginx config directories if they don't exist for some reason.
        //       Check their default permissions to set the right ones.
        //       Use this technique to improve robustness for other config uploading scripts, nginx first of all.

        $file = "/etc/nginx/sites-available/{$project->projectName}_placeholder.conf";

        if (! $this->sendString(
            $file,
            ConfigView::render(
                $project->hasSsl() ? 'nginx.server_placeholder_ssl' : 'nginx.server_placeholder',
                [
                    'project' => $project,
                    'server' => $server,
                ]
            ),
        )) {
            throw new ServerScriptException('Command to upload Nginx placeholder config has failed.');
        }
    }
}
