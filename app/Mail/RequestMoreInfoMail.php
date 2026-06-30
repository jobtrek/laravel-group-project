<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// ShouldQueue is mandatory per project convention — mails must never block the request.
class RequestMoreInfoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // public properties on a Mailable are automatically available in the view.
    public function __construct(
        public readonly Project $project,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Informations complémentaires requises — {$this->project->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.request-more-info',
        );
    }
}