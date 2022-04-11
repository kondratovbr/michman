<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class HideBillingIfDisabled
{
    public function handle(Request $request, Closure $next): Response
    {
        ray(
            $request->route(),
            $request->route()->getName(),
            $request->routeIs('spark.portal'),
            $request->path(),
        );

        if ($request->routeIs('spark.portal')) {
            //
        }

        return $next($request);
    }
}
