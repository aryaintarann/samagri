<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KanbanCard extends Model
{
    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Get the column that owns this card.
     */
    public function column(): BelongsTo
    {
        return $this->belongsTo(KanbanColumn::class, 'column_id');
    }

    /**
     * Get the users assigned to this card (many-to-many).
     */
    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kanban_card_user', 'kanban_card_id', 'user_id')->withTimestamps();
    }

    /**
     * Get the attachments for this card.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(KanbanCardAttachment::class, 'card_id');
    }

    /**
     * Get the priority badge color class.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'bg-red-100 text-red-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'low' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the comments for this card.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(KanbanCardComment::class, 'card_id')->orderBy('created_at', 'desc');
    }
}
