<?php declare(strict_types=1);

namespace App\Listeners;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail extends AbstractEventListener implements ShouldQueue
{
    public function handle(Verified $event): void
    {
        /** @var User $user */
        $user = $event->user;

        if (! $user->hasFeature('onboarding_emails'))
            return;

        if (! $user->canReceiveEmails())
            return;

        Mail::to($user)->send(new WelcomeEmail);
    }
}