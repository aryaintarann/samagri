<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDocument extends Model
{
    protected $guarded = [];

    /**
     * Category labels for display.
     */
    public const CATEGORY_LABELS = [
        'quotation' => 'Quotation Template',
        'spk' => 'SPK / Kontrak Kerjasama',
        'bast' => 'BAST (Berita Acara Serah Terima)',
        'srd' => 'SRD (Software Requirement Document)',
    ];

    /**
     * Allowed file extensions.
     */
    public const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

    /**
     * Get the project that owns this document.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who uploaded this document.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORY_LABELS[$this->category] ?? $this->category;
    }

    /**
     * Get file size in human readable format.
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file icon based on type.
     */
    public function getFileIconAttribute(): string
    {
        $ext = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => 'fa-file-pdf text-red-500',
            'doc', 'docx' => 'fa-file-word text-blue-500',
            'xls', 'xlsx' => 'fa-file-excel text-green-500',
            default => 'fa-file text-gray-500',
        };
    }
}
