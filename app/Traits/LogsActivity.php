<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Log an activity.
     *
     * @param string $action
     * @param string|null $description
     * @return void
     */
    public function logActivity($action, $description = null)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }
}
