<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCapture extends Model
{
    protected $table = 'asset_captures';
    public $timestamps = false;

    protected $fillable = [
        'routine_maintenance_id',
        'plant_id',
        'performed_by',
        'performed_at',
        'visit_date',
        'incident_no',
        'service_sheet_no',
        'flc',
        'uin',
        'poc_name',
        'poc_phone',
        'user_email',
        'unit_address',
        'postcode',
        'country',
        'notes',
        'source_form_id',
        'source_form_email',
        'source_form_name',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'datetime',
            'visit_date' => 'date',
        ];
    }

    public function routine()
    {
        return $this->belongsTo(RoutineMaintenance::class, 'routine_maintenance_id');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }

    public function tanks()
    {
        return $this->hasMany(AssetCaptureTank::class, 'capture_id');
    }

    public function atg()
    {
        return $this->hasOne(AssetCaptureAtg::class, 'capture_id');
    }

    public function srfTask()
    {
        return $this->hasOne(ServiceReportTask::class, 'asset_capture_id');
    }

    public function masterControllers()
    {
        return $this->hasMany(AssetCaptureMasterController::class, 'capture_id');
    }

    public function slaveControllers()
    {
        return $this->hasMany(AssetCaptureSlaveController::class, 'capture_id');
    }

    public function pumps()
    {
        return $this->hasMany(AssetCapturePump::class, 'capture_id');
    }

    public function photos()
    {
        return $this->hasMany(AssetCapturePhoto::class, 'capture_id');
    }
}
