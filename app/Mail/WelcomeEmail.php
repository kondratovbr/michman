<?php declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
    ) {}

    /** Get the message envelope. */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Michman!',
        );
    }

    /** Get the message content definition. */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
        );
    }

    /** Get the attachments for the message. */
    public function attachments(): array
    {
        return [];
    }
}