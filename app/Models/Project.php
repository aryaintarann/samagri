<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $guarded = [];

    protected $casts = [
        'deadline' => 'date',
        'active' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the Kanban board for this project.
     */
    public function kanbanBoard(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(KanbanBoard::class);
    }

    /**
     * Get the documents for this project.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }
}
