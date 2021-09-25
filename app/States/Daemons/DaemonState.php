<?php declare(strict_types=1);

namespace App\States\Daemons;

use App\States\AbstractModelState;
use App\States\Daemons\Transitions\ToFailed;
use Spatie\ModelStates\StateConfig;

abstract class DaemonState extends AbstractModelState
{
    public static string $langKey = 'servers.daemons.states';

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Starting::class)
            ->allowTransition([Starting::class, Restarting::class], Active::class)
            ->allowTransition(Stopping::class, Stopped::class)
            ->allowTransition([Stopping::class, Stopped::class], Starting::class)
            ->allowTransition([Starting::class, Active::class, Restarting::class], Stopping::class)
            ->allowTransition([
                Starting::class,
                Active::class,
                Stopping::class,
                Stopped::class,
                Failed::class,
            ], Restarting::class)
            ->allowTransition([
                Starting::class,
                Restarting::class,
                Active::class,
            ], Failed::class, ToFailed::class)
            ->allowTransition([
                Starting::class,
                Restarting::class,
                Active::class,
                Stopping::class,
                Stopped::class,
                Failed::class,
            ], Deleting::class);
    }
}
