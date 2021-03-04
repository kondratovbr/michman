<?php declare(strict_types=1);

if (! function_exists('version')) {
    /**
     * Get a string with a full current version of the app.
     */
    function version(): string
    {
        return (string) config('app.version');
    }
}
