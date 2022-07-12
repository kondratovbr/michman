<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Notifications\TestNotification;
use Illuminate\Contracts\View\View;
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

    /** Try websockets with a notification for the currently authed user. */
    public function websockets(): void
    {
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
}
