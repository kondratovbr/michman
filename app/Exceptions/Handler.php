<?php

namespace App\Exceptions;

use App\Mail\UncaughtThrowableAdminMail;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Mail;
use Throwable;

class Handler extends ExceptionHandler
{
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
        $this->reportable(function (Throwable $throwable) {
            Mail::to(config('app.alert_email'))->send(new UncaughtThrowableAdminMail($throwable));
        });
    }
}
