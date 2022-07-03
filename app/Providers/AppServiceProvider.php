<?php declare(strict_types=1);

namespace App\Providers;

use App\Models;
use App\Support\Arr;
use App\Support\ConfigViewFactory;
use App\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Date;
use Carbon\CarbonImmutable;
use Illuminate\View\FileViewFinder;
use Illuminate\View\ViewServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Switch to using the immutable versions of Carbon objects across the whole application.
        Date::use(CarbonImmutable::class);

        $this->configureEncryption();

        /**
         * Register a custom View factory to use Blade for server config files.
         *
         * @see ViewServiceProvider
         */
        $this->app->singleton('config-view-factory', function ($app) {
            $resolver = $app['view.engine.resolver'];

            $finder = new FileViewFinder(
                $app['files'],
                $app['config']['view.config-views-paths'],
                Arr::keys($app['config']['view.config-views-extensions']),
            );

            $factory = new ConfigViewFactory(
                $resolver,
                $finder,
                $app['events'],
                $app['config']['view.config-views-extensions'],
            );

            $factory->setContainer($app);

            $factory->share('app', $app);

            return $factory;
        });
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => Models\User::class,
            'team' => Models\Team::class,
        ]);

        /*
         * This will rollback any transactions that may have been
         * left open after a job has failed.
         * @see https://laravel.com/docs/8.x/queues#job-events
         */
        Queue::looping(function () {
            while (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
        });
    }

    private function configureEncryption(): void
    {
        if (empty(config('app.encryption_key')))
            throw new RuntimeException('ENCRYPTION_KEY is not set.');

        Model::encryptUsing(new Encrypter(
            base64_decode(Str::after(config('app.encryption_key'), 'base64:')),
            config('app.cipher'),
        ));
    }
}
