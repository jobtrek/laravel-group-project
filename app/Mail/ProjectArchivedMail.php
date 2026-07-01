<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectArchivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;

    public $project;

    public function __construct(User $user, Project $project)
    {
        $this->user = $user;
        $this->project = $project;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Projet archivé',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.ProjectArchivedMail',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
