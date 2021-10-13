<?php declare(strict_types=1);

namespace App\States\Workers\Transitions;

use App\Models\Worker;
use App\Notifications\Workers\WorkerFailedNotification;
use App\States\Workers\Failed;
use Illuminate\Support\Facades\DB;
use Spatie\ModelStates\Transition;

class ToFailed extends Transition
{
    public function __construct(
        protected Worker $worker,
    ) {}

    public function handle(): Worker
    {
        return DB::transaction(function (): Worker {
            $worker = $this->worker->freshLockForUpdate();

            $worker->state = new Failed($worker);
            $worker->save();

            $worker->user->notify(new WorkerFailedNotification($worker));

            return $worker;
        }, 5);
    }
}
