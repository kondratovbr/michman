<?php declare(strict_types=1);

namespace App\Actions\Pythons;

use App\DataTransferObjects\PythonData;
use App\Jobs\Pythons\InstallPythonJob;
use App\Models\Python;
use App\Models\Server;

class StorePythonAction
{
    public function execute(PythonData $data, Server $server, bool $sync = false): Python
    {
        $attributes = $data->toArray();

        $attributes['status'] = Python::STATUS_INSTALLING;

        /** @var Python $python */
        $python = $server->pythons()->create($attributes);

        if ($sync) {
            InstallPythonJob::dispatchSync($python, true);
            $python->refresh();
        } else {
            InstallPythonJob::dispatch($python);
        }

        return $python;
    }
}
