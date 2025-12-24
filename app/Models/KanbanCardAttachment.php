<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KanbanCardAttachment extends Model
{
    protected $guarded = [];

    /**
     * Get the card that owns this attachment.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(KanbanCard::class, 'card_id');
    }

    /**
     * Get the file extension.
     */
    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
    }

    /**
     * Get the icon class based on file type.
     */
    public function getIconClassAttribute(): string
    {
        $extension = $this->extension;

        return match (true) {
            in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => 'fa-image text-blue-500',
            in_array($extension, ['pdf']) => 'fa-file-pdf text-red-500',
            in_array($extension, ['mp4', 'avi', 'mov', 'wmv']) => 'fa-file-video text-purple-500',
            in_array($extension, ['mp3', 'wav', 'ogg']) => 'fa-file-audio text-green-500',
            in_array($extension, ['doc', 'docx']) => 'fa-file-word text-blue-600',
            in_array($extension, ['xls', 'xlsx']) => 'fa-file-excel text-green-600',
            in_array($extension, ['zip', 'rar', '7z']) => 'fa-file-archive text-yellow-600',
            default => 'fa-file text-gray-500',
        };
    }
}
