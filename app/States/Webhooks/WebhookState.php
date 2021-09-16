<?php declare(strict_types=1);

namespace App\States\Webhooks;

use App\States\AbstractModelState;
use Spatie\ModelStates\StateConfig;

abstract class WebhookState extends AbstractModelState
{
    public static string $langKey = 'projects.hooks.states';

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Enabling::class)
            ->allowTransition(Enabling::class, Enabled::class)
            ->allowTransition([Enabling::class, Enabled::class], Deleting::class);
    }
}
