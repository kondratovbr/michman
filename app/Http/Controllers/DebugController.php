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
}
