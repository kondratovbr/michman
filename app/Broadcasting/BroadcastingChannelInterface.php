<?php declare(strict_types=1);

namespace App\Broadcasting;

interface BroadcastingChannelInterface
{
    /**
     * Get the channel's definition string.
     */
    public static function definition(): string;
}
