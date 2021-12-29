<?php declare(strict_types=1);

namespace App\Actions\Pythons;

use App\Jobs\Pythons\DeletePythonJob;
use App\Models\Python;
use Illuminate\Support\Facades\DB;

// TODO: IMPORTANT! Cover with tests.

class DeletePythonAction
{
    public function execute(Python $python): void
    {
        DB::transaction(function () use ($python) {
            $python = $python->freshLockForUpdate();

            if ($python->isDeleting())
                return;

            $python->status = Python::STATUS_DELETING;

            DeletePythonJob::dispatch($python);
        }, 5);
    }
}
