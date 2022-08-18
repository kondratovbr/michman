<?php declare(strict_types=1);

namespace App\States\Servers;

use App\States\AbstractModelState;
use Spatie\ModelStates\StateConfig;

class ServerState extends AbstractModelState
{
    public static string $langKey = 'servers.states';

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Creating::class)
            ->allowTransition(Creating::class, Preparing::class)
            ->allowTransition(Preparing::class, Configuring::class)
            ->allowTransition(Configuring::class, Ready::class)
            ->allowTransition(Ready::class, Deleting::class)
            ->allowTransition([Creating::class, Preparing::class, Configuring::class], Failed::class)
            // Recovery
            ->allowTransitions([
                [Failed::class, Creating::class],
                [Failed::class, Preparing::class],
                [Failed::class, Configuring::class],
                [Failed::class, Ready::class],
            ]);
    }
}
