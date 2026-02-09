<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCaptureTank extends Model
{
    protected $table = 'asset_capture_tanks';
    public $timestamps = false;

    protected $fillable = [
        'capture_id',
        'tank_number',
        'product_code',
        'product_name',
        'capacity_litres',
        'notes',
    ];

    public function capture()
    {
        return $this->belongsTo(AssetCapture::class, 'capture_id');
    }
}
