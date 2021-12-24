<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Facades\ConfigView;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class UploadPlaceholderPageNginxConfigScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $this->exec("mkdir -p /etc/nginx/sites-available && mkdir -p /etc/nginx/sites-enabled");

        $file = "/etc/nginx/sites-available/michman_placeholder.conf";

        if (! $this->sendString(
            $file,
            ConfigView::render(
                'nginx.server_placeholder_ssl',
                [
                    'server' => $server,
                ]
            ),
        )) {
            throw new ServerScriptException('Command to upload Nginx placeholder config has failed.');
        }
    }
}
