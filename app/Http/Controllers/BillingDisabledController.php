<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class BillingDisabledController extends AbstractController
{
    /** Show the "Billing Temporarily Disabled" page. */
    public function __invoke(): View
    {
        return view('billing-disabled');
    }
}
