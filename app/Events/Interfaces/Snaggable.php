<?php declare(strict_types=1);

namespace App\Events\Interfaces;

use App\Services\LogSnag\SnagChannel;
use App\Services\LogSnag\SnagEvent;

/**
 * @property-read bool $snagNotify
 * @property-read string|null $snagIcon
 *
 * @see \App\Events\Traits\Snaggable
 */
interface Snaggable
{
    public function getSnagChannel(): SnagChannel;
    public function getSnagEvent(): SnagEvent;
    public function getSnagDescription(): string|null;
}
