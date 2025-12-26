<?php

namespace App\Http\Controllers;

use App\Http\Requests\Kanban\MoveCardRequest;
use App\Http\Requests\Kanban\StoreCardRequest;
use App\Http\Requests\Kanban\StoreColumnRequest;
use App\Http\Requests\Kanban\UpdateCardRequest;
use App\Http\Resources\KanbanCardResource;
use App\Models\KanbanBoard;
use App\Models\KanbanCard;
use App\Models\Project;
use App\Services\KanbanService;
use Illuminate\Http\Request;

class KanbanController extends Controller
{
    protected KanbanService $kanbanService;

    public function __construct(KanbanService $kanbanService)
    {
        $this->kanbanService = $kanbanService;
    }

    public function index(Project $project)
    {
        // Ensure board exists
        $board = $project->kanbanBoard ?? $project->kanbanBoard()->create(['name' => $project->name . ' Board']);

        // Eager load everything needed for the view
        $board->load(['columns.cards.users', 'columns.cards.attachments', 'columns.cards.comments']);

        return view('projects.kanban', compact('project', 'board'));
    }

    public function storeColumn(StoreColumnRequest $request)
    {
        $column = $this->kanbanService->createColumn($request->validated());

        if ($request->ajax()) {
            return response()->json(['message' => 'Column created successfully', 'column' => $column]);
        }

        return back()->with('success', 'Column created successfully.');
    }

    public function storeCard(StoreCardRequest $request)
    {
        $card = $this->kanbanService->createCard($request->validated());

        if ($request->ajax()) {
            // Re-load relationships for the resource
            $card->load(['users', 'attachments', 'comments']);
            return response()->json([
                'message' => 'Card created successfully',
                'card' => KanbanCardResource::make($card)->resolve()
            ]);
        }

        return back()->with('success', 'Card created successfully.');
    }

    public function updateCard(UpdateCardRequest $request, KanbanCard $card)
    {
        $updatedCard = $this->kanbanService->updateCard($card, $request->validated());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Card updated successfully',
                'card' => KanbanCardResource::make($updatedCard->fresh(['users', 'attachments', 'comments']))->resolve()
            ]);
        }

        return back()->with('success', 'Card updated successfully.');
    }

    public function moveCard(MoveCardRequest $request)
    {
        $this->kanbanService->moveCard($request->validated());

        return response()->json(['message' => 'Card moved successfully']);
    }

    public function destroyCard(KanbanCard $card)
    {
        $this->kanbanService->deleteCard($card);

        if (request()->ajax()) {
            return response()->json(['message' => 'Card deleted successfully']);
        }

        return back()->with('success', 'Card deleted successfully.');
    }
}
