<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineTask extends Model
{
    protected $fillable = [
        'display_order',
        'task_text',
        'name',
        'active',
    ];

    public function getTaskTextAttribute(): string
    {
        return $this->attributes['task_text']
            ?? $this->attributes['name']
            ?? '';
    }
}
