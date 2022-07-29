<?php declare(strict_types=1);

use App\Models\User;
use App\Facades\Auth;

if (! function_exists('filesize_for_humans')) {
    /** Convert filesize in bytes into a human-readable formatted string with rounding. */
    function sizeForHumansRounded(int $bytes, $precision = 2): string
    {
        static $units = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];
        static $step = 1024;
        $i = 0;
        while (($bytes / $step) > 0.9) {
            $bytes = $bytes / $step;
            $i++;
        }
        return round($bytes, $precision) . ' ' . __('units.' . $units[$i]);
    }
}

if (! function_exists('spaceToNbsp')) {
    /** Convert all spaces in a string to non-breaking spaces. */
    function spaceToNbsp(string $string): string
    {
        return str_replace(' ', "\xc2\xa0", $string);
    }
}

if (! function_exists('siteName')) {
    /** Get a properly formatted app name. */
    function siteName(): string
    {
        return spaceToNbsp((string) config('app.name'));
    }
}

if (! function_exists('title')) {
    // TODO: Do I really use it?
    /**
     * Create a properly formatted page title with a separator.
     */
    function title(string $title = null): ?string
    {
        if (empty($title))
            return null;

        return config('view.page_title_separator') . $title;
    }
}

if (! function_exists('user')) {
    /** Get the currently authenticated user. */
    function user(): User|null
    {
        return Auth::user();
    }
}

if (! function_exists('classes')) {
    // TODO: Would be nice to also properly "merge" them?
    /** Combine all provided CSS classes into a single space-separated string. */
    function classes(string|array ...$classes): string
    {
        return array_reduce($classes, function (string $carry, string|array $item) {
            if ($carry !== '')
                $carry .= ' ';

            if (is_string($item))
                $carry .= $item;
            else
                $carry .= classes(...$item);

            return $carry;
        }, '');
    }
}

if (! function_exists('docsUrl')) {
    /** Get a URL for the separate docs site. */
    function docsUrl(string $page = null): string
    {
        if (! $page)
            return config('app.docs_url');

        return
            rtrim(config('app.docs_url'), '/')
            . '/'
            . ltrim($page, '/');
    }
}
