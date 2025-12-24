<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\KanbanBoard;
use App\Models\KanbanColumn;
use App\Models\KanbanCard;
use App\Models\KanbanCardAttachment;
use App\Models\KanbanCardComment;
use App\Models\User;
use App\Notifications\KanbanCardAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KanbanController extends Controller
{
    /**
     * Allowed file extensions for attachments.
     */
    private array $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'mp3', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'];

    /**
     * Display the Kanban board for a project.
     */
    public function show(Project $project)
    {
        $board = $project->kanbanBoard()->with(['columns.cards.assignees', 'columns.cards.attachments'])->first();

        // Create board if it doesn't exist (for existing projects)
        if (!$board) {
            $board = $project->kanbanBoard()->create(['name' => $project->name . ' Board']);
            $board->createDefaultColumns();
            $board->load(['columns.cards.assignees', 'columns.cards.attachments']);
        }

        $users = User::all();

        return view('projects.kanban', compact('project', 'board', 'users'));
    }

    /**
     * Store a new column.
     */
    public function storeColumn(Request $request)
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:kanban_boards,id',
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        // Get max position
        $maxPosition = KanbanColumn::where('board_id', $validated['board_id'])->max('position') ?? -1;
        $validated['position'] = $maxPosition + 1;

        $column = KanbanColumn::create($validated);

        return response()->json([
            'message' => 'Column created successfully',
            'column' => $column,
        ]);
    }

    /**
     * Update a column.
     */
    public function updateColumn(Request $request, KanbanColumn $column)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $column->update($validated);

        return response()->json([
            'message' => 'Column updated successfully',
            'column' => $column,
        ]);
    }

    /**
     * Delete a column.
     */
    public function destroyColumn(KanbanColumn $column)
    {
        $column->delete();

        return response()->json([
            'message' => 'Column deleted successfully',
        ]);
    }

    /**
     * Reorder columns.
     */
    public function reorderColumns(Request $request)
    {
        $validated = $request->validate([
            'columns' => 'required|array',
            'columns.*.id' => 'required|exists:kanban_columns,id',
            'columns.*.position' => 'required|integer|min:0',
        ]);

        foreach ($validated['columns'] as $columnData) {
            KanbanColumn::where('id', $columnData['id'])->update(['position' => $columnData['position']]);
        }

        return response()->json([
            'message' => 'Columns reordered successfully',
        ]);
    }

    /**
     * Store a new card.
     */
    public function storeCard(Request $request)
    {
        $validated = $request->validate([
            'column_id' => 'required|exists:kanban_columns,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'color' => 'nullable|string|max:7',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:20480', // 20MB max per file
        ]);

        // Get max position
        $maxPosition = KanbanCard::where('column_id', $validated['column_id'])->max('position') ?? -1;

        $card = KanbanCard::create([
            'column_id' => $validated['column_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'due_date' => $validated['due_date'] ?? null,
            'color' => $validated['color'] ?? null,
            'position' => $maxPosition + 1,
        ]);

        // Sync assignees and send notifications
        if (!empty($validated['assignees'])) {
            $card->assignees()->sync($validated['assignees']);

            // Send notifications to assignees
            $assignedBy = auth()->user() ? auth()->user()->name : 'System';
            $assignees = User::whereIn('id', $validated['assignees'])->get();

            \Log::info('Sending notifications to assignees', [
                'card_id' => $card->id,
                'assignees_count' => $assignees->count(),
                'current_user' => auth()->id()
            ]);

            foreach ($assignees as $assignee) {
                try {
                    $assignee->notify(new KanbanCardAssigned($card, $assignedBy));
                    \Log::info('Notification sent to user', ['user_id' => $assignee->id]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send notification', [
                        'user_id' => $assignee->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $extension = strtolower($file->getClientOriginalExtension());
                    if (in_array($extension, $this->allowedExtensions)) {
                        $path = $file->store('kanban-attachments', 'public');
                        $card->attachments()->create([
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            }
        }

        $card->load(['assignees', 'attachments']);

        return response()->json([
            'message' => 'Card created successfully',
            'card' => $card,
        ]);
    }

    /**
     * Get a single card for editing.
     */
    public function getCard(KanbanCard $card)
    {
        $card->load(['assignees', 'attachments', 'comments.user']);

        // Format comments for frontend
        $formattedComments = $card->comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at->diffForHumans(),
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
            ];
        });

        return response()->json([
            'card' => $card,
            'comments' => $formattedComments,
        ]);
    }

    /**
     * Update a card.
     */
    public function updateCard(Request $request, KanbanCard $card)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'color' => 'nullable|string|max:7',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:20480',
        ]);

        $card->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'due_date' => $validated['due_date'] ?? null,
            'color' => $validated['color'] ?? null,
        ]);

        // Get current assignees before update
        $currentAssigneeIds = $card->assignees->pluck('id')->toArray();
        $newAssigneeIds = $validated['assignees'] ?? [];

        // Find newly added assignees (those in new list but not in current)
        $addedAssigneeIds = array_diff($newAssigneeIds, $currentAssigneeIds);

        // Sync assignees
        $card->assignees()->sync($newAssigneeIds);

        // Send notifications to NEW assignees only
        if (!empty($addedAssigneeIds)) {
            $assignedBy = auth()->user() ? auth()->user()->name : 'System';
            $newAssignees = User::whereIn('id', $addedAssigneeIds)->get();

            foreach ($newAssignees as $assignee) {
                // Don't notify the user who is editing the card
                if (auth()->id() !== $assignee->id) {
                    $assignee->notify(new KanbanCardAssigned($card, $assignedBy));
                }
            }
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $extension = strtolower($file->getClientOriginalExtension());
                    if (in_array($extension, $this->allowedExtensions)) {
                        $path = $file->store('kanban-attachments', 'public');
                        $card->attachments()->create([
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            }
        }

        $card->load(['assignees', 'attachments']);

        return response()->json([
            'message' => 'Card updated successfully',
            'card' => $card,
        ]);
    }

    /**
     * Delete a card.
     */
    public function destroyCard(KanbanCard $card)
    {
        // Delete attachments from storage
        foreach ($card->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $card->delete();

        return response()->json([
            'message' => 'Card deleted successfully',
        ]);
    }

    /**
     * Delete a single attachment.
     */
    public function destroyAttachment(KanbanCardAttachment $attachment)
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json([
            'message' => 'Attachment deleted successfully',
        ]);
    }

    /**
     * Move a card to a different column or position.
     */
    public function moveCard(Request $request)
    {
        $validated = $request->validate([
            'card_id' => 'required|exists:kanban_cards,id',
            'column_id' => 'required|exists:kanban_columns,id',
            'position' => 'required|integer|min:0',
        ]);

        $card = KanbanCard::findOrFail($validated['card_id']);
        $oldColumnId = $card->column_id;
        $oldPosition = $card->position;

        // Update cards in old column (shift positions up)
        if ($oldColumnId != $validated['column_id']) {
            KanbanCard::where('column_id', $oldColumnId)
                ->where('position', '>', $oldPosition)
                ->decrement('position');
        }

        // Update cards in new column (shift positions down)
        KanbanCard::where('column_id', $validated['column_id'])
            ->where('position', '>=', $validated['position'])
            ->when($oldColumnId == $validated['column_id'], function ($query) use ($oldPosition) {
                return $query->where('position', '<', $oldPosition);
            })
            ->increment('position');

        // Update the card
        $card->update([
            'column_id' => $validated['column_id'],
            'position' => $validated['position'],
        ]);

        return response()->json([
            'message' => 'Card moved successfully',
            'card' => $card->fresh()->load(['assignees', 'attachments']),
        ]);
    }

    /**
     * Store a new comment for a card.
     */
    public function storeComment(Request $request, KanbanCard $card)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $comment = $card->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        $comment->load('user');

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at->diffForHumans(),
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
            ],
        ]);
    }

    /**
     * Delete a comment.
     */
    public function destroyComment(KanbanCardComment $comment)
    {
        // Only the comment owner or admin can delete
        if (auth()->id() !== $comment->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }
}
