<?php declare(strict_types=1);

namespace Tests\Dummies\McDummyStates;

use App\States\AbstractModelState;
use Spatie\ModelStates\StateConfig;

class McDummyState extends AbstractModelState
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(McFirst::class)
            ->allowTransition(McFirst::class, McSecond::class)
            ->allowTransition(McSecond::class, McThird::class);
    }
}
