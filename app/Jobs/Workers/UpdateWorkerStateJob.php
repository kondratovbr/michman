<?php declare(strict_types=1);

namespace App\Jobs\Workers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Worker;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\UpdateWorkerStateScript;
use App\States\Workers\Deleting;
use App\States\Workers\Failed;
use App\States\Workers\Starting;
use Illuminate\Support\Facades\DB;

class UpdateWorkerStateJob extends AbstractRemoteServerJob
{
    protected Worker $worker;

    public function __construct(Worker $worker)
    {
        parent::__construct($worker->server);

        $this->worker = $worker->withoutRelations();
    }

    public function handle(UpdateWorkerStateScript $script): void
    {
        DB::transaction(function () use ($script) {
            $server = $this->server->freshSharedLock();
            $worker = $this->worker->freshLockForUpdate();

            if ($worker->state->is(Deleting::class))
                return;

            try {
                $worker->state = $script->execute($server, $worker);
                $worker->save();
            } catch (ServerScriptException) {
                $worker->state->transitionTo(Failed::class);
            }

            // If the worker is still starting, i.e. hasn't failed or successfully started yet -
            // repeat this job a bit later.
            if ($worker->state->is(Starting::class))
                $this->release(30);
        });
    }
}
