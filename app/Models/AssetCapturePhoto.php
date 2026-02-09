<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCapturePhoto extends Model
{
    protected $table = 'asset_capture_photos';
    public $timestamps = false;

    protected $fillable = [
        'capture_id',
        'url',
        'description',
    ];

    public function capture()
    {
        return $this->belongsTo(AssetCapture::class, 'capture_id');
    }
}
