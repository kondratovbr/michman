<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Facades\ConfigView;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class EnablePlaceholderSiteScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        // TODO: CRITICAL! Use mkdir here to create those two nginx config directories if they don't exist for some reason.
        //       Check their default permissions to set the right ones.
        //       Use this technique to improve robustness for other config uploading scripts, nginx first of all.

        $available = "/etc/nginx/sites-available";
        $enabled = "/etc/nginx/sites-enabled";
        $file = "{$project->projectName}_placeholder.conf";

        if (! $this->sendString(
            "{$available}/{$file}",
            ConfigView::render('nginx.server_placeholder', [
                'project' => $project,
                'server' => $server,
            ]),
        )) {
            throw new ServerScriptException('Command to upload Nginx placeholder config has failed.');
        }

        if ($this->exec("ln -sf {$available}/{$file} {$enabled}/{$file}") === false)
            throw new ServerScriptException('Failed to create a symlink to Nginx placeholder config.');
    }
}
