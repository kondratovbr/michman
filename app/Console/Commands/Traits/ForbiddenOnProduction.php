<?php declare(strict_types=1);

namespace App\Console\Commands\Traits;

use App\Console\Commands\AbstractCommand;

/**
 * Trait ForbiddenOnProduction for console commands
 *
 * Will force a command to fail if run in an environment 'production'.
 *
 * @mixin AbstractCommand
 */
trait ForbiddenOnProduction
{
    protected function isForbiddenOnProduction(): bool
    {
        return true;
    }
}
