<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class DebugController extends AbstractController
{
    public function __construct()
    {
        $this->middleware('debug');
    }

    /**
     * Show the list of registered routes of the application.
     */
    public function routes(): View
    {
        $routes = Route::getRoutes();

        return view('debug.routes', [
            'routes' => $routes,
        ]);
    }

    /**
     * Show a standard phpinfo page.
     */
    public function phpInfo()
    {
        phpinfo();
    }

    /**
     * Show a completely blank page.
     */
    public function blank(): View
    {
        return view('debug.blank');
    }

    /**
     * Show a Pusher test page.
     */
    public function pusher(): View
    {
        return view('debug.pusher');
    }
}
