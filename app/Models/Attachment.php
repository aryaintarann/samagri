<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $guarded = [];

    /**
     * Get the parent attachable model (project or sop).
     */
    public function attachable()
    {
        return $this->morphTo();
    }
}
