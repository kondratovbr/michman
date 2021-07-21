<?php declare(strict_types=1);

namespace App\Jobs\Pythons;

use App\Events\Pythons\PythonPatchedEvent;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Python;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// TODO: CRITICAL! Test and cover with tests!

class PatchPythonJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Python $python;

    public function __construct(Python $python)
    {
        $this->setQueue('servers');

        $this->python = $python->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Python $python */
            $python = Python::query()
                ->lockForUpdate()
                ->findOrFail($this->python->getKey());

            if (! $python->isUpdating())
                return;

            $scriptClass = (string) config("servers.python.{$python->version}.scripts_namespace") . '\PatchPythonScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No patching script exists for this version of Python.');

            $patchVersion = $scriptClass::run($python->server);

            $python->status = Python::STATUS_INSTALLED;
            $python->patchVersion = $patchVersion;
            $python->save();

            event(new PythonPatchedEvent($python));
        }, 5);
    }
}
