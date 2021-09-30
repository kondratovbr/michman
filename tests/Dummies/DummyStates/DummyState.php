<?php declare(strict_types=1);

namespace Tests\Dummies\DummyStates;

use App\States\AbstractModelState;
use Spatie\ModelStates\StateConfig;

class DummyState extends AbstractModelState
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(First::class)
            ->allowTransition(First::class, Second::class)
            ->allowTransition(Second::class, Third::class);
    }
}
