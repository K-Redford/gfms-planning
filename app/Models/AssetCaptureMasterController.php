<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCaptureMasterController extends Model
{
    protected $table = 'asset_capture_masters';
    public $timestamps = false;

    protected $fillable = [
        'capture_id',
        'serial',
        'software_version',
        'comm_method',
        'display_type',
        'keypad_type',
        'asset_tag_faceplate',
        'asset_tag_keypad',
        'asset_tag_key_reader',
        'qty',
    ];

    public function capture()
    {
        return $this->belongsTo(AssetCapture::class, 'capture_id');
    }
}
