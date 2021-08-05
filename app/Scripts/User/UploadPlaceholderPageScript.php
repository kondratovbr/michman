<?php declare(strict_types=1);

namespace App\Scripts\User;

use App\Models\Project;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use Illuminate\Support\Facades\View;
use phpseclib3\Net\SFTP;
use RuntimeException;

class UploadPlaceholderPageScript extends AbstractServerScript
{
    public function execute(Server $server, Project $project, SFTP $ssh = null): void
    {
        $this->init($server, $ssh, $project->serverUsername);

        if (! $this->sendString(
            "{$project->michmanDir}/public/index.html",
            View::make('michman-placeholder')->render(),
        )) {
            throw new RuntimeException('Command to upload placeholder page has failed.');
        }
    }
}
