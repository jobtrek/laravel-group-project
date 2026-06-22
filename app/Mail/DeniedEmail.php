<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeniedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $name)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Projet refusé',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.Denied',
            with: ['name' => $this->name],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
