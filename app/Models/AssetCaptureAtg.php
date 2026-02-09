<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCaptureAtg extends Model
{
    protected $table = 'asset_capture_atg';
    public $timestamps = false;

    protected $fillable = [
        'capture_id',
        'atg_type',
        'notes',
    ];

    public function capture()
    {
        return $this->belongsTo(AssetCapture::class, 'capture_id');
    }
}
