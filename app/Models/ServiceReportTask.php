<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReportTask extends Model
{
    protected $table = 'service_report_tasks';

    protected $fillable = [
        'implementation_completion_id',
        'asset_capture_id',
        'plant_id',
        'engineer_user_id',
        'service_report_id',
        'status',
    ];

    public function completion()
    {
        return $this->belongsTo(ImplementationCompletion::class, 'implementation_completion_id');
    }

    public function assetCapture()
    {
        return $this->belongsTo(AssetCapture::class, 'asset_capture_id');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_user_id');
    }

    public function report()
    {
        return $this->belongsTo(ServiceReport::class, 'service_report_id');
    }
}
