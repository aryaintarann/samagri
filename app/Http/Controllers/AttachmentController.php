<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Display (download/stream) the specified attachment.
     */
    public function show(Attachment $attachment)
    {
        // Optional: Check permissions here if needed
        // e.g., if ($attachment->attachable->user_id !== auth()->id()) abort(403);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404);
        }

        // Create a file response
        $path = Storage::disk('public')->path($attachment->file_path);

        if (request()->has('download')) {
            return response()->download($path, $attachment->file_name);
        }

        return response()->file($path);
    }
}
