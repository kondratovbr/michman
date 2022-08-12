<?php declare(strict_types=1);

namespace App\Jobs\Pythons;

use App\Events\Pythons\PythonInstalledEvent;
use App\Jobs\AbstractRemoteServerJob;
use App\Models\Python;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallPythonJob extends AbstractRemoteServerJob
{
    protected Python $python;

    public function __construct(Python $python, bool $sync = false)
    {
        parent::__construct($python->server)->sync($sync);

        $this->python = $python->withoutRelations();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $python = $this->python->freshLockForUpdate();
            $server = $this->server->freshSharedLock();

            if ($python->isInstalled())
                return;

            $scriptClass = config("servers.python.versions.$python->version.scripts_namespace") . '\InstallPythonScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No installation script exists for this version of Python.');

            $patchVersion = $scriptClass::run($server);

            $python->status = Python::STATUS_INSTALLED;
            $python->patchVersion = $patchVersion;
            $python->save();

            event(new PythonInstalledEvent($python));
        }, 5);
    }
}
