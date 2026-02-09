<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    protected $table = 'plants';

    public $timestamps = false;

    public function implementationCompletion()
    {
        return $this->hasOne(ImplementationCompletion::class, 'plant_id');
    }

    public function routineMaintenances()
    {
        return $this->hasMany(RoutineMaintenance::class, 'plant_id');
    }
}
