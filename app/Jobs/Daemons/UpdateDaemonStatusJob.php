<?php declare(strict_types=1);

namespace App\Jobs\Daemons;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Daemon;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\UpdateDaemonStatusScript;
use Illuminate\Support\Facades\DB;

class UpdateDaemonStatusJob extends AbstractRemoteServerJob
{
    protected Daemon $daemon;

    public function __construct(Daemon $daemon)
    {
        parent::__construct($daemon->server);

        $this->daemon = $daemon->withoutRelations();
    }

    public function handle(UpdateDaemonStatusScript $script): void
    {
        DB::transaction(function () use($script) {
            $server = $this->server->freshSharedLock();
            $daemon = $this->daemon->freshLockForUpdate();

            try {
                $daemon->status = $script->execute($server, $daemon);
            } catch (ServerScriptException) {
                $daemon->status = Daemon::STATUS_FAILED;
            }

            $daemon->save();

            // If the daemon is still starting, i.e. hasn't failed or successfully started yet -
            // repeat this job a bit later.
            if ($daemon->isStarting())
                $this->release(30);
        }, 5);
    }
}
