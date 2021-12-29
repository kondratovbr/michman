<?php declare(strict_types=1);

namespace App\Jobs\Pythons;

use App\Events\Pythons\PythonRemovedEvent;
use App\Jobs\AbstractRemoteServerJob;
use App\Models\Python;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

// TODO: IMPORTANT! Cover with tests.

class DeletePythonJob extends AbstractRemoteServerJob
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
            $server = $this->server->freshLockForUpdate();
            $python = $this->python->freshLockForUpdate();

            if (! $python->isDeleting()) {
                Log::warning('Tried to delete a Python that has a status other than "deleting".');
                return;
            }

            $scriptClass = config("servers.python.{$python->version}.scripts_namespace") . '\DeletePythonScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No deletion script exists for this version of Python.');

            $scriptClass::run($server);

            $python->delete();

            event(new PythonRemovedEvent($python));
        }, 5);
    }
}
