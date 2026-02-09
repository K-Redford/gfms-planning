<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReport extends Model
{
    protected $table = 'service_report_forms';

    protected $fillable = [
        'serial_number',
        'plant_id',
        'company_name',
        'site_address',
        'date_of_visit',
        'time_on_site',
        'time_off_site',
        'site_time_minutes',
        'travel_time_hours',
        'mileage',
        'order_number',
        'transflo_ref',
        'charge_type',
        'equipment_type',
        'reported_fault',
        'report_details',
        'departure_statuses',
        'software_changes',
        'engineer_signature_path',
        'customer_signature_path',
        'customer_print_name',
        'customer_rank_civ',
        'customer_email',
        'notes',
        'engineer_user_id',
    ];

    protected $casts = [
        'date_of_visit' => 'date',
        'departure_statuses' => 'array',
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_user_id');
    }

    public function parts()
    {
        return $this->hasMany(ServiceReportPart::class, 'service_report_id');
    }
}
