<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestedService extends Model
{
    protected $table = 'requested_services';
    protected $primaryKey = 'requested_service_id';
    public $timestamps = true;

    protected $fillable = [
        'request_id',
        'service_id',
        'quantity',
    ];

    // Relationship: belongs to a requisition form
    public function requisitionForm()
    {
        return $this->belongsTo(RequisitionForm::class, 'request_id', 'request_id');
    }

    // Relationship: belongs to an extra service
    public function extraService()
    {
        return $this->belongsTo(ExtraService::class, 'service_id', 'service_id');
    }
}
