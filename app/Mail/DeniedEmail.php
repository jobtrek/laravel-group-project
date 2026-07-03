<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeniedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(private string $name)
    {
        //
        $this->afterCommit();
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
