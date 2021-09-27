<?php declare(strict_types=1);

namespace App\States\Certificates;

use App\States\AbstractModelState;
use Spatie\ModelStates\StateConfig;

class CertificateState extends AbstractModelState
{
    public static string $langKey = 'servers.ssl.states';

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Installing::class)
            ->allowTransition(Installing::class, Installed::class)
            ->allowTransition([Installing::class, Installed::class], Deleting::class);
    }
}
