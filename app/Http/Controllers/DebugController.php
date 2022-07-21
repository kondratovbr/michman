<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\Users\FlashMessageEvent;
use App\Facades\Auth;
use App\Notifications\TestNotification;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

class DebugController extends AbstractController
{
    public function __construct()
    {
        $this->middleware('debug');
    }

    /** Show the list of registered routes of the application. */
    public function routes(): View
    {
        $routes = Route::getRoutes();

        return view('debug.routes', [
            'routes' => $routes,
        ]);
    }

    /** Show a standard phpinfo page. */
    public function phpInfo(): void
    {
        phpinfo();
    }

    /** Try websockets with a flash message for the currently authed user. */
    public function websockets(): void
    {
        flash('If you see this - websockets work!', FlashMessageEvent::STYLE_SUCCESS);
    }

    /** Send a test notification to the currently authed user. */
    public function notification(): void
    {
        if (Auth::guest())
            throw new Exception('Authenticate to be able to send a test notification.');

        user()->notify(new TestNotification);
    }

    /** Show a completely empty HTML page. */
    public function empty(): View
    {
        return view('debug.empty');
    }

    /** Show a blank page. */
    public function blank(): View
    {
        return view('debug.blank');
    }

    /** Show a Pusher test page. */
    public function pusher(): View
    {
        return view('debug.pusher');
    }

    /** Dump all environment values. */
    public function env(): void
    {
        dump($_ENV);
    }

    /** Dump all config values. */
    public function config(): void
    {
        dump(config()->all());
    }

    /** Throw an exception. */
    public function exception(): void
    {
        throw new Exception('This is a blank test exception.');
    }

    /** Send a test email to the admin. */
    public function email(): void
    {
        Mail::raw(
            'This is a test email from ' . config('app.name') . '. If you can see this - emailing works!',
            function (Message $email) {
                $email
                    ->from(
                        config('mail.from.address'),
                        config('mail.from.name'),
                    )
                    ->to(config('app.admin_email'))
                    ->subject('Test Email');
            }
        );
    }

    /** Store a key-value pair in the default cache. */
    public function cachePut(Request $request): void
    {
        Cache::put($request->get('key'), $request->get('value'), 60 * 60);
    }

    /** Dump a requested value from cache. */
    public function cacheGet(Request $request): void
    {
        dump(Cache::get($request->get('key')));
    }

    /** Dump the default cache store instance. */
    public function cacheStore(): void
    {
        dump(Cache::store());
    }
}
