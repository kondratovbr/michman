<?php declare(strict_types=1);

namespace App\Providers;

use App\Services\MailerLite;
use Illuminate\Support\ServiceProvider;

class MailingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MailerLite::class, function () {
            return new MailerLite(
                token: (string) config('mail.mailerlite_api_key')
            );
        });
    }
}
