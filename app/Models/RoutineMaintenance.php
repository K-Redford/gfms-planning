<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineMaintenance extends Model
{
    protected $fillable = [
        'plant_id',
        'routine_year',
        'performed_at',
        'performed_by',
        'asset_capture_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'date',
        ];
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function items()
    {
        return $this->hasMany(RoutineMaintenanceItem::class, 'routine_maintenance_id');
    }

    public function assetCapture()
    {
        return $this->hasOne(AssetCapture::class, 'routine_maintenance_id');
    }
}
