<?php declare(strict_types=1);

namespace App\Providers;

use App\Models\Team;
use App\Models\User;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /** @var string[] The policy mappings for the application. */
    protected $policies = [
        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // TODO: CRITICAL! Make sure to protect all admin/support routes and properly configure this one as well. This one is a temporary solution.
        // Authorization gate used by beyondcode/laravel-websockets for restricting access to the stats dashboard.
        Gate::define('viewWebSocketsDashboard', function (User $user = null) {
            return config('app.env') == 'local'
                && in_array($user->email, [
                    'kondratovbr@gmail.com',
                    'admin@example.com',
                ])
                && ! is_null($user->emailVerifiedAt);
        });

        //
    }
}
