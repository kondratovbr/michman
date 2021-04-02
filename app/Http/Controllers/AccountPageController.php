<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class AccountPageController extends AbstractController
{
    /**
     * Show the user's account page.
     */
    public function __invoke(string $show = 'profile'): View
    {
        return view('account.show', ['show' => $show]);
    }
}
