<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KanbanBoard extends Model
{
    protected $guarded = [];

    /**
     * Get the project that owns this board.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the columns for this board.
     */
    public function columns(): HasMany
    {
        return $this->hasMany(KanbanColumn::class, 'board_id')->orderBy('position');
    }

    /**
     * Create default columns for a new board.
     */
    public function createDefaultColumns(): void
    {
        $defaultColumns = [
            ['name' => 'To Do', 'position' => 0, 'color' => '#6B7280'],
            ['name' => 'In Progress', 'position' => 1, 'color' => '#3B82F6'],
            ['name' => 'Review', 'position' => 2, 'color' => '#F59E0B'],
            ['name' => 'Done', 'position' => 3, 'color' => '#10B981'],
        ];

        foreach ($defaultColumns as $column) {
            $this->columns()->create($column);
        }
    }
}
