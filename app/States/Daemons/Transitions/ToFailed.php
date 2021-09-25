<?php declare(strict_types=1);

namespace App\States\Daemons\Transitions;

use App\Models\Daemon;
use App\Notifications\Daemons\DaemonFailedNotification;
use App\States\Daemons\Failed;
use Illuminate\Support\Facades\DB;
use Spatie\ModelStates\Transition;

class ToFailed extends Transition
{
    public function __construct(
        protected Daemon $daemon,
    ) {}

    public function handle(): Daemon
    {
        return DB::transaction(function (): Daemon {
            $daemon = $this->daemon->freshLockForUpdate();

            $daemon->state = new Failed($daemon);
            $daemon->save();

            $daemon->user->notify(new DaemonFailedNotification($daemon));

            return $daemon;
        }, 5);
    }
}
