<?php declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Services\MailerLite;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddSubscriberToMailingService extends AbstractEventListener implements ShouldQueue
{
    public function __construct(
        private readonly MailerLite $mailer,
    ) {}

    public function handle(Verified $event): void
    {
        /** @var User $user */
        $user = $event->user;

        if (! $user->hasFeature('onboarding_emails'))
            return;

        if (! $user->canReceiveEmails())
            return;

        $this->mailer->upsertSubscriber($user->email);
    }
}
