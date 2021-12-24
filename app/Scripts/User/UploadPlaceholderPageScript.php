<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use Illuminate\Support\Facades\View;
use phpseclib3\Net\SFTP;

class UploadPlaceholderPageScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh, config('servers.worker_user'));

        // Create a directory for the placeholder page if it doesn't exist.
        if ($this->exec("mkdir -p {$server->publicWorkerDir}") === false)
            throw new ServerScriptException('mkdir command has failed.');

        if (! $this->sendString(
            "{$server->publicWorkerDir}/index.html",
            View::make('michman-placeholder')->render(),
        )) {
            throw new ServerScriptException('Command to upload placeholder page has failed.');
        }
    }
}
