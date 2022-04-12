<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class HideBillingIfDisabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if ( ! config('app.billing_enabled') && $request->routeIs('spark.portal'))
            return redirect()->route('billing-disabled');

        return $next($request);
    }
}
