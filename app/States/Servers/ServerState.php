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
            ->allowTransition(Configuring::class, Ready::class);
    }
}