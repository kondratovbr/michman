<?php declare(strict_types=1);

namespace App\Actions\Pythons;

use App\DataTransferObjects\PythonData;
use App\Jobs\Servers\InstallPythonJob;
use App\Models\Python;

// TODO: CRITICAL! Cover with tests!

class StorePythonAction
{
    public function execute(PythonData $data): Python
    {
        /** @var Python $python */
        $python = $data->server->pythons()->create($data->toArray());

        InstallPythonJob::dispatch($python);

        return $python;
    }
}
