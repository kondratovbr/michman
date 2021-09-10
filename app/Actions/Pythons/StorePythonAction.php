<?php declare(strict_types=1);

namespace App\Actions\Pythons;

use App\Jobs\Pythons\InstallPythonJob;
use App\Models\Python;
use App\Models\Server;

class StorePythonAction
{
    public function execute(string $version, Server $server, bool $sync = false): Python
    {
        /** @var Python $python */
        $python = $server->pythons()->create([
            'version' => $version,
            'status' => Python::STATUS_INSTALLING,
        ]);

        if ($sync) {
            InstallPythonJob::dispatchSync($python, true);
            $python->refresh();
        } else {
            InstallPythonJob::dispatch($python);
        }

        return $python;
    }
}
