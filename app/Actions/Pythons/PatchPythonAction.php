<?php declare(strict_types=1);

namespace App\Actions\Pythons;

use App\Jobs\Servers\PatchPythonJob;
use App\Models\Python;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Test and cover with tests!

class PatchPythonAction
{
    public function execute(Python $python): Python
    {
        return DB::transaction(function () use ($python) {
            /** @var Python $python */
            $python = Python::query()
                ->lockForUpdate()
                ->findOrFail($python->getKey());

            $python->status = Python::STATUS_UPDATING;
            $python->save();

            PatchPythonJob::dispatch($python);

            return $python;
        }, 5);
    }
}
