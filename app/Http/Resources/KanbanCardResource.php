<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KanbanCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'column_id' => $this->column_id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority, // Assuming Enum is cast in Model, otherwise this is string
            'due_date' => $this->due_date ? $this->due_date->format('Y-m-d') : null,
            'color' => $this->color,
            'position' => $this->position,
            'assignees' => $this->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar_url, // Or whatever prop is used
                ];
            }),
            'attachments' => $this->attachments->map(function ($att) {
                return [
                    'id' => $att->id,
                    'file_name' => $att->file_name,
                    'file_path' => asset('storage/' . $att->file_path),
                    'file_type' => $att->file_type,
                ];
            }),
            'comments_count' => $this->comments_count ?? $this->comments()->count(),
            'attachment_count' => $this->attachments_count ?? $this->attachments()->count(),
        ];
    }
}
