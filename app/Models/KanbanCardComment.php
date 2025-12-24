<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KanbanCardComment extends Model
{
    protected $guarded = [];

    /**
     * Get the card that this comment belongs to.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(KanbanCard::class, 'card_id');
    }

    /**
     * Get the user who wrote the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
