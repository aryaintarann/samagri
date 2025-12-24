<?php

namespace App\Mail;

use App\Models\KanbanCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KanbanCardAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $card;
    public $assignedBy;
    public $project;
    public $kanbanUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(KanbanCard $card, $assignedBy = null)
    {
        $this->card = $card;
        $this->assignedBy = $assignedBy ?? 'System';
        $this->project = $card->column->board->project ?? null;
        $this->kanbanUrl = $this->project ? route('projects.kanban', $this->project->id) : url('/');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Card Assignment: ' . $this->card->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.kanban-card-assigned',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
