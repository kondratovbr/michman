<?php declare(strict_types=1);

use App\Collections\EloquentCollection;
use App\Support\Arr;
use App\Support\Str;

if (! function_exists('version')) {
    /**
     * Get a string with a full current version of the app.
     */
    function version(): string
    {
        return (string) config('app.version');
    }
}

if (! function_exists('isDebug')) {
    /**
     * Check if the app is in debug mode.
     */
    function isDebug(): bool
    {
        return config('app.debug') === true;
    }
}

if (! function_exists('generateRandomName')) {
    /**
     * Generate a random project-like name for servers, projects, etc.
     *
     * Uses dictionary config file.
     */
    function generateRandomName(string $separator = '-', bool $includeNumber = false, int $number = null): string
    {
        // We give a 1/10 chance of generating a Star Wars related name. Just for fun.
        $result = rand(0, 9)
            ? implode($separator, [
                Str::lower(Arr::random(config('dictionary.adjectives'))),
                Str::lower(Arr::random(Arr::merge(
                    config('dictionary.animals'),
                    config('dictionary.nouns'),
                ))),
            ])
            // Str::kebab() doubles the dashes in source strings, which can exist in these strings,
            // so we replace double dashes with single ones.
            : Str::replace(
                '--', '-',
                Str::kebab(Arr::random(config('dictionary.star-wars')))
            );

        if ($includeNumber)
            $result .= $separator . $number ?? rand(1, 100);

        return $result;
    }
}

if (! function_exists('')) {
    /**
     * Generate a pseudo-random float between 0 and 1 inclusively.
     */
    function randomFloat(): float
    {
        return (float) rand() / (float) getrandmax();
    }
}

if (! function_exists('collection')) {
    /**
     * Turn the provided array into an instance of custom EloquentCollection.
     */
    function collection(array $items): EloquentCollection
    {
        return new EloquentCollection($items);
    }
}
