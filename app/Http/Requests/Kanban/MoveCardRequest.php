<?php

namespace App\Http\Requests\Kanban;

use Illuminate\Foundation\Http\FormRequest;

class MoveCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_id' => ['required', 'exists:kanban_cards,id'],
            'column_id' => ['required', 'exists:kanban_columns,id'],
            'position' => ['required', 'integer', 'min:0'],
        ];
    }
}
