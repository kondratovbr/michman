<?php declare(strict_types=1);

namespace App\Console\Commands\Traits;

use App\Console\Commands\AbstractCommand;

/**
 * Trait ProtectedOnProduction for console commands
 *
 * Will require a confirmation to run a command on production.
 *
 * @mixin AbstractCommand
 */
trait ProtectedOnProduction
{
    protected function isProtectedOnProduction(): bool
    {
        return true;
    }
}
