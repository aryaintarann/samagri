<?php

namespace App\Http\Requests\Kanban;

use App\Enums\KanbanCardPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'column_id' => ['required', 'exists:kanban_columns,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', new Enum(KanbanCardPriority::class)],
            'due_date' => ['nullable', 'date'],
            'assignees' => ['nullable', 'array'],
            'assignees.*' => ['exists:users,id'],
            'color' => ['nullable', 'string', 'max:7'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:20480'], // 20MB
        ];
    }
}
