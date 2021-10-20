<?php declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    /** Get the email verification notification mail message for the given URL. */
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->theme('dark')
            ->subject(__('auth.verification-email.subject'))
            ->line(__('auth.verification-email.first-line'))
            ->action(__('auth.verification-email.action'), $url)
            ->line(__('auth.verification-email.second-line'));
    }
}
