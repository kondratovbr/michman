<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /** @var array A list of exception types with their corresponding custom log levels. */
    protected $levels = [
        //
    ];

    /** @var string[] A list of the exception types that are not reported. */
    protected $dontReport = [
        //
    ];

    /** @var string[] A list of the inputs that are never flashed for validation exceptions. */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /** Register the exception handling callbacks for the application. */
    public function register(): void
    {
        /*
         * This is the place to register custom exception handlers and reporters.
         * See: https://laravel.com/docs/9.x/errors
         */

        //

    }
}
