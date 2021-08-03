<?php declare(strict_types=1);

namespace App\Jobs\Pythons;

use App\Events\Pythons\PythonInstalledEvent;
use App\Jobs\AbstractRemoteServerJob;
use App\Models\Python;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// TODO: CRITICAL! Cover with tests!

class InstallPythonJob extends AbstractRemoteServerJob
{
    protected Python $python;

    public function __construct(Python $python)
    {
        parent::__construct($python->server);

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
                ->with('server')
                ->lockForUpdate()
                ->findOrFail($this->python->getKey());

            if ($python->isInstalled())
                return;

            $scriptClass = (string) config("servers.python.{$python->version}.scripts_namespace") . '\InstallPythonScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No installation script exists for this version of Python.');

            $patchVersion = $scriptClass::run($python->server);

            $python->status = Python::STATUS_INSTALLED;
            $python->patchVersion = $patchVersion;
            $python->save();

            event(new PythonInstalledEvent($python));
        }, 5);
    }
}
