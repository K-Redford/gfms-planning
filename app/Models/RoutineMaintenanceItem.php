<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineMaintenanceItem extends Model
{
    protected $fillable = [
        'routine_maintenance_id',
        'routine_task_id',
        'completed',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
        ];
    }

    public function routine()
    {
        return $this->belongsTo(RoutineMaintenance::class, 'routine_maintenance_id');
    }

    public function task()
    {
        return $this->belongsTo(RoutineTask::class, 'routine_task_id');
    }
}
