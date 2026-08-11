<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectsPermanentlyDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{id: int, title: string, stage: string, reason: string}>  $deletedProjects
     */
    public function __construct(
        public User $user,
        public array $deletedProjects,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Projets supprimés définitivement',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.ProjectsPermanentlyDeletedMail',
        );
    }
}