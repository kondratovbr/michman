<?php declare(strict_types=1);

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /*
         * This command tells Fortify to ignore the built-in routes from the file in vendor directory.
         * We must do it here, because the RouteServiceProvider is loaded after the FortifyServiceProvider
         * from the "vendor" directory.
         */
        Fortify::ignoreRoutes();
    }

    public function boot(): void
    {
        // TODO: IMPORTANT! Cover logging in with some tests.
        Fortify::authenticateUsing(function (Request $request): User|null {
            /** @var User|null $user */
            $user = User::query()
                ->where('email', $request->email)
                ->notDeleting()
                ->first();

            if (
                $user &&
                Hash::check($request->password, $user->password)
            ) {
                return $user;
            }

            return null;
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
