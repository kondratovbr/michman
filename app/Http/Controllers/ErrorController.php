<?php declare(strict_types=1);

namespace App\Http\Controllers;

/*
 * This controller is needed to be able to serve error pages on their specific URLs,
 * which can be useful for errors that occur on the web-server (nginx) level.
 * It is also used to redirect users to error pages from Livewire components.
 */

// TODO: IMPORTANT! Is all these error codes needed in this application?

class ErrorController extends AbstractController
{
    public function error401(): void
    {
        abort(401);
    }

    public function error403(): void
    {
        abort(403);
    }

    public function error404(): void
    {
        abort(404);
    }

    public function error413(): void
    {
        abort(413);
    }

    public function error419(): void
    {
        abort(419);
    }

    public function error429(): void
    {
        abort(429);
    }

    public function error500(): void
    {
        abort(500);
    }

    public function error503(): void
    {
        abort(503);
    }
}
