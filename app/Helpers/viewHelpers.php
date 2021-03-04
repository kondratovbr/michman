<?php declare(strict_types=1);

if (! function_exists('filesize_for_humans')) {
    /**
     * Convert filesize in bytes into a human-readable formatted string with rounding.
     */
    function filesize_for_humans_rounded(int $bytes, $precision = 2): string
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
    /**
     * Convert all spaces in a string to non-breaking spaces.
     */
    function spaceToNbsp(string $string): string
    {
        return str_replace(' ', "\xc2\xa0", $string);
    }
}

if (! function_exists('siteName')) {
    /**
     * Get a properly formatted app name.
     */
    function siteName(): string
    {
        return spaceToNbsp((string) config('app.name'));
    }
}

if (! function_exists('title')) {
    /**
     * Create a properly formatted page title with a separator.
     */
    function title(string $title = null): ?string
    {
        if (empty($title))
            return null;

        return (string) config('view.page_title_separator') . $title;
    }
}
