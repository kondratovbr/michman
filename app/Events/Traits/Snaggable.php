<?php declare(strict_types=1);

namespace App\Events\Traits;

/**
 * @see \App\Events\Interfaces\Snaggable
 */
trait Snaggable
{
    public bool $snagNotify = false;
    public string|null $snagIcon = null;

    public function getSnagDescription(): string|null
    {
        return null;
    }
}
