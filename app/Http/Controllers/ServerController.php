<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ServerController extends AbstractController
{
    /**
     * Show the list of user's servers.
     */
    public function index(): View
    {
        return view('servers.index');
    }
}
