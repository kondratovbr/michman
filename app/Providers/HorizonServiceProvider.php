<?php declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        // Enable dark theme for Horizon. It's a bit janky.
        // Horizon::night();

        Horizon::routeMailNotificationsTo((string) config('app.admin_email'));

        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
        // Horizon::routeSmsNotificationsTo('15556667777');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function (User $user) {
            return $user->isAdmin();
        });
    }
}
