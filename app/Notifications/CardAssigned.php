<?php

namespace App\Notifications;

use App\Models\KanbanCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CardAssigned extends Notification
{
    use Queueable;

    public function __construct(public KanbanCard $card)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Keeping it simple for now, can add 'mail' later
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('You have been assigned to a card.')
            ->action('View Card', url('/projects/' . $this->card->column->board->project_id . '/kanban')) // Approximate URL
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'card_id' => $this->card->id,
            'title' => $this->card->title,
            'message' => 'You have been assigned to card: ' . $this->card->title,
        ];
    }
}
