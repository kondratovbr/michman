<?php declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/*
 * NOTE: This email was never used and kept for reference.
 */

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

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
            markdown: 'emails.md.welcome',
        );
    }

    /** Get the attachments for the message. */
    public function attachments(): array
    {
        return [];
    }
}
