<?php declare(strict_types=1);

namespace App\Listeners;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail extends AbstractEventListener
{
    public function handle(Registered|Verified $event): void
    {
        /** @var User $user */
        $user = $event->user;

        if (! $user->hasFeature('onboarding_emails'))
            return;

        if (is_null($user->emailVerifiedAt))
            return;

        Mail::to($user)->send(new WelcomeEmail);
    }
}
