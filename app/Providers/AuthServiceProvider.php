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

    public function boot(): void
    {
        $this->registerPolicies();

        // TODO: CRITICAL! DEPLOYMENT. Make sure to protect all admin/support routes and properly configure this one as well.
        // Authorization gate used by beyondcode/laravel-websockets for restricting access to the stats dashboard.
        Gate::define('viewWebSocketsDashboard', function (User $user = null) {
            return $user->isAdmin();
        });

        //
    }
}
