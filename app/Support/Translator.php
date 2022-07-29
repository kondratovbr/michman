<?php declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Translation\Translator as BaseTranslator;

/**
 * Extends the built-in class to also log cases when a required translation doesn't exist.
 */
class Translator extends BaseTranslator
{
    /*
     * TODO: IMPORTANT! Should try enabling these two after the app is finished.
     *       Note: Jetstream (maybe something else as well) uses no-key lang strings, so
     *       the logic here will spam log messages if that isn't changed.
     */
    private const IGNORE_NULL = true;
    private const IGNORE_MISSING = true;

    public function get($key, array $replace = [], $locale = null, $fallback = true): string|array|null
    {
        if ( ! static::IGNORE_NULL && is_null($key))
            Log::notice('Translator::get() method is called with a null key.');

        if (! static::IGNORE_MISSING) {
            $main = parent::get($key, $replace, $locale, false);

            if ($main === $key)
                Log::notice("Translation doesn't exist. Key: '$key', locale: '" . ($locale ?? $this->locale()) . "'");
        }

        return parent::get($key, $replace, $locale, $fallback);
    }
}
