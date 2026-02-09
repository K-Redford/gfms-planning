<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImplementationCompletion extends Model
{
    protected $table = 'implementation_completions';

    protected $fillable = [
        'plant_id',
        'implemented_at',
        'implemented_by',
    ];

    protected $casts = [
        'implemented_at' => 'date',
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'implemented_by');
    }
}
