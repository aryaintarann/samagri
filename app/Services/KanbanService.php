<?php

namespace App\Services;

use App\Enums\KanbanCardPriority;
use App\Models\KanbanBoard;
use App\Models\KanbanCard;
use App\Models\KanbanColumn;
use App\Models\User;
use App\Notifications\CardAssigned;
use App\Traits\LogsActivity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class KanbanService
{
    use LogsActivity;

    public function createColumn(array $data): KanbanColumn
    {
        return DB::transaction(function () use ($data) {
            $board = KanbanBoard::findOrFail($data['board_id']);
            $maxPosition = $board->columns()->max('position') ?? -1;

            $column = $board->columns()->create([
                'name' => $data['name'],
                'color' => $data['color'] ?? '#E5E7EB',
                'position' => $maxPosition + 1,
            ]);

            $this->logActivity('Created Column', 'Created column: ' . $column->name);

            return $column;
        });
    }

    public function createCard(array $data): KanbanCard
    {
        return DB::transaction(function () use ($data) {
            $card = KanbanCard::create([
                'column_id' => $data['column_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $data['priority'] ?? KanbanCardPriority::MEDIUM->value,
                // Handle different date formats or nulls if needed, cast handled in Request usually
                'due_date' => $data['due_date'] ?? null,
                'color' => $data['color'] ?? null,
                'position' => KanbanCard::where('column_id', $data['column_id'])->max('position') + 1,
            ]);

            if (isset($data['attachments'])) {
                $this->handleAttachments($card, $data['attachments']);
            }

            if (isset($data['assignees'])) {
                $this->syncAssignees($card, $data['assignees']);
            }

            $this->logActivity('Created Card', 'Created card: ' . $card->title);

            return $card;
        });
    }

    public function updateCard(KanbanCard $card, array $data): KanbanCard
    {
        return DB::transaction(function () use ($card, $data) {
            $card->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $data['priority'] ?? $card->priority, // Keep old if not provided, though request validates logic
                'due_date' => $data['due_date'] ?? null,
                'color' => $data['color'] ?? null,
            ]);

            if (isset($data['attachments'])) {
                $this->handleAttachments($card, $data['attachments']);
            }

            if (isset($data['assignees'])) {
                $this->syncAssignees($card, $data['assignees']);
            }

            $this->logActivity('Updated Card', 'Updated card: ' . $card->title);

            return $card;
        });
    }

    public function moveCard(array $data): void
    {
        DB::transaction(function () use ($data) {
            $card = KanbanCard::findOrFail($data['card_id']);
            $targetColumnId = $data['column_id'];
            $newPosition = $data['position'];

            $originalColumnId = $card->column_id;
            $originalPosition = $card->position;

            // If moving within same column
            if ($originalColumnId == $targetColumnId) {
                if ($originalPosition == $newPosition) {
                    return; // No change
                }

                // Shift other cards
                if ($originalPosition < $newPosition) {
                    KanbanCard::where('column_id', $targetColumnId)
                        ->whereBetween('position', [$originalPosition + 1, $newPosition])
                        ->decrement('position');
                } else {
                    KanbanCard::where('column_id', $targetColumnId)
                        ->whereBetween('position', [$newPosition, $originalPosition - 1])
                        ->increment('position');
                }
            } else {
                // Moving to different column
                // Shift down cards in the source column
                KanbanCard::where('column_id', $originalColumnId)
                    ->where('position', '>', $originalPosition)
                    ->decrement('position');

                // Shift up cards in the target column
                KanbanCard::where('column_id', $targetColumnId)
                    ->where('position', '>=', $newPosition)
                    ->increment('position');
            }

            $card->update([
                'column_id' => $targetColumnId,
                'position' => $newPosition
            ]);

            $this->logActivity('Moved Card', "Moved card {$card->title} to new position");
        });
    }

    public function deleteCard(KanbanCard $card): void
    {
        DB::transaction(function () use ($card) {
            $title = $card->title;
            // Shift later cards up
            KanbanCard::where('column_id', $card->column_id)
                ->where('position', '>', $card->position)
                ->decrement('position');

            $card->delete();
            $this->logActivity('Deleted Card', 'Deleted card: ' . $title);
        });
    }

    protected function handleAttachments(KanbanCard $card, array $files): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $path = $file->store('card-attachments', 'public');
                $card->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
    }

    protected function syncAssignees(KanbanCard $card, array $assigneeIds): void
    {
        $assigneeIds = array_filter($assigneeIds);
        $currentIds = $card->users()->pluck('user_id')->toArray();
        $newIds = array_diff($assigneeIds, $currentIds);

        $card->users()->sync($assigneeIds);

        if (!empty($newIds)) {
            $users = User::whereIn('id', $newIds)->get();
            // Assuming we have Project context or can resolve it. 
            // Notifications often behave better with explicit objects.
            // If CardAssigned expects a Card, we can pass it.
            Notification::send($users, new CardAssigned($card));
        }
    }
}
