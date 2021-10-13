<?php declare(strict_types=1);

namespace App\States\Workers;

use App\States\AbstractModelState;
use App\States\Workers\Transitions\ToFailed;
use Spatie\ModelStates\StateConfig;

class WorkerState extends AbstractModelState
{
    public static string $langKey = 'projects.queue.states';

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Starting::class)
            ->allowTransition([Starting::class, Active::class, Failed::class], Starting::class)
            ->allowTransition(Starting::class, Active::class)
            ->allowTransition([Starting::class, Active::class], Failed::class, ToFailed::class)
            ->allowTransition([Starting::class, Active::class, Failed::class], Deleting::class);
    }
}
