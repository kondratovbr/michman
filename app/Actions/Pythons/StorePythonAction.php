<?php declare(strict_types=1);

namespace App\Actions\Pythons;

use App\DataTransferObjects\PythonData;
use App\Jobs\Servers\InstallPythonJob;
use App\Models\Python;
use App\Models\Server;

// TODO: CRITICAL! Test and cover with tests!

class StorePythonAction
{
    public function execute(PythonData $data, Server $server): Python
    {
        $attributes = $data->toArray();

        $attributes['status'] = Python::STATUS_INSTALLING;

        /** @var Python $python */
        $python = $server->pythons()->create($attributes);

        InstallPythonJob::dispatch($python);

        return $python;
    }
}
