<?php

namespace App\Notifications;

use App\Models\KanbanCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KanbanCardAssigned extends Notification
{
    use Queueable;

    protected $card;
    protected $assignedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(KanbanCard $card, $assignedBy = null)
    {
        $this->card = $card;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new \App\Mail\KanbanCardAssignedMail($this->card, $this->assignedBy))
            ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $project = $this->card->column->board->project ?? null;

        return [
            'type' => 'kanban_card_assigned',
            'card_id' => $this->card->id,
            'card_title' => $this->card->title,
            'project_id' => $project?->id,
            'project_name' => $project?->name,
            'message' => 'You have been assigned to card: ' . $this->card->title,
            'assigned_by' => $this->assignedBy ?? 'System',
            'url' => $project ? route('projects.kanban', $project->id) : null,
        ];
    }
}
