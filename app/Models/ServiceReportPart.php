<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReportPart extends Model
{
    protected $table = 'service_report_form_parts';

    protected $fillable = [
        'service_report_id',
        'part_description',
        'stock_code',
        'quantity',
        'price_each',
        'total_price',
    ];

    public function report()
    {
        return $this->belongsTo(ServiceReport::class, 'service_report_id');
    }
}
