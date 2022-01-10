<?php declare(strict_types=1);

use App\Collections\EloquentCollection;
use App\Events\Users\FlashMessageEvent;
use App\Facades\Auth;
use App\Models\User;
use App\Support\Arr;
use App\Support\Str;

if (! function_exists('version')) {
    /** Get a string with a full current version of the app. */
    function version(): string
    {
        return (string) config('app.version');
    }
}

if (! function_exists('isDebug')) {
    /** Check if the app is in debug mode. */
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

if (! function_exists('randomFloat')) {
    /** Generate a pseudo-random float between 0 and 1 inclusively. */
    function randomFloat(): float
    {
        return (float) rand() / (float) getrandmax();
    }
}

if (! function_exists('collection')) {
    /** Turn the provided array into an instance of custom EloquentCollection. */
    function collection(array $items): EloquentCollection
    {
        return new EloquentCollection($items);
    }
}

if (! function_exists('trimRelativePath')) {
    /**
     * Trim a string that represents a relative Unix path.
     *
     * Remove spaces, empty characters, slashes from the beginning and end,
     * also replace double slashes with single ones.
     */
    function trimRelativePath(string|null $path): string|null
    {
        if (is_null($path))
            return null;

        $path = trim($path, " \t\n\r\0\x0B/");

        while (Str::contains($path, '//')) {
            $path = Str::replace('//', '/', $path);
        }

        return $path;
    }
}

if (! function_exists('getClassName')) {
    /** Get only the last part of the class name. */
    function getClassName(string $classname): string|false|int
    {
        $pos = strrpos($classname, '\\');

        if ($pos)
            return substr($classname, $pos + 1);

        return $pos;
    }
}

if (! function_exists('classImplements')) {
    /** Check that a given class implements a given interface. */
    function classImplements(string $class, string $interface): bool
    {
        $interfaces = class_implements($class);

        return in_array($interface, $interfaces);
    }
}

if (! function_exists('trans_try')) {
    /** Try multiple lang keys and return the first string found or null if nothing found. */
    function trans_try(array|string $keys, array $replace = [], string|null $locale = null): string|null
    {
        foreach (Arr::wrap($keys) as $key) {
            if (trans()->has($key))
                return trans()->get($key, $replace, $locale);
        }

        return null;
    }
}

if (! function_exists('flash')) {
    /** Flash a message for the user using a FlashMessageEvent. */
    function flash(string $message, string $style = null, User $user = null): void
    {
        event(new FlashMessageEvent(
            $user ?? Auth::user(),
            $message,
            $style,
        ));
    }
}
