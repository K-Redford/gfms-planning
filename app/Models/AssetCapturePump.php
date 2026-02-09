<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCapturePump extends Model
{
    protected $table = 'asset_capture_pumps';
    public $timestamps = false;

    protected $fillable = [
        'capture_id',
        'vendor',
        'model',
        'qty',
    ];

    public function capture()
    {
        return $this->belongsTo(AssetCapture::class, 'capture_id');
    }
}
