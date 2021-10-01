<?php declare(strict_types=1);

namespace App\Jobs\Pythons;

use App\Events\Pythons\PythonPatchedEvent;
use App\Jobs\AbstractRemoteServerJob;
use App\Models\Python;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PatchPythonJob extends AbstractRemoteServerJob
{
    protected Python $python;

    public function __construct(Python $python)
    {
        parent::__construct($python->server);

        $this->python = $python->withoutRelations();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $python = $this->python->freshLockForUpdate();
            $server = $this->server->freshSharedLock();

            if (! $python->isUpdating())
                return;

            $scriptClass = (string) config("servers.python.{$python->version}.scripts_namespace") . '\PatchPythonScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No patching script exists for this version of Python.');

            $patchVersion = $scriptClass::run($server);

            $python->status = Python::STATUS_INSTALLED;
            $python->patchVersion = $patchVersion;
            $python->save();

            event(new PythonPatchedEvent($python));
        }, 5);
    }
}
