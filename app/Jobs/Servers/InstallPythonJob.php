<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Events\Pythons\PythonInstalledEvent;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Python;
use App\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// TODO: CRITICAL! Cover with tests!

class InstallPythonJob extends AbstractJob
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
                ->with('server')
                ->lockForUpdate()
                ->findOrFail($this->python->getKey());

            if (! Arr::hasValue(config("servers.types.{$python->server->type}.install"), 'python')) {
                $this->fail(new RuntimeException('This type of server should not have Python installed.'));
                return;
            }

            if ($python->server->pythons()->where('version', $python->version)->get()->isNotEmpty()) {
                $this->fail(new RuntimeException('This Python version model is already created for this server.'));
                return;
            }

            $python = $python->server->pythons()->create([
                'version' => $python->version,
            ]);

            $scriptClass = (string) config("servers.python.{$python->version}.scripts_namespace") . '\InstallPythonScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No installation script exists for this version of Python.');

            $script = App::make($scriptClass);

            $script->execute($python->server);

            event(new PythonInstalledEvent($python));
        }, 5);
    }
}
