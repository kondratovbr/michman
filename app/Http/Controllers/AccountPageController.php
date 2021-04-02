<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AccountPageController extends AbstractController
{
    /**
     * Show the user's account page.
     */
    public function __invoke(Request $request): View
    {
        return view('account.show', [
            'show' => $request->query('show', 'profile'),
        ]);
    }
}
