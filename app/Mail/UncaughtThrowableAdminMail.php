<?php declare(strict_types=1);

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class UncaughtThrowableAdminMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Throwable $exception,
    ) {}

    /** @return $this */
    public function build(): static
    {
        return $this
            ->from(config('app.alert_from_email'))
            ->to(config('app.alert_email'))
            ->subject('Michman App Alert')
            ->text('mail.text.uncaught-exception-alert');
    }
}
