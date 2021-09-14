<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Translation\TranslationServiceProvider as BaseProvider;
use App\Support\Translator;

class TranslationServiceProvider extends BaseProvider
{
    public function register(): void
    {
        /*
         * Exactly the same code as in the base provider,
         * but with our custom Translator.
         */

        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }
}
