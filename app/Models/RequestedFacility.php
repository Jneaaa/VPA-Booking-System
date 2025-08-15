<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestedFacility extends Model
{
    use HasFactory;
    protected $fillable = [
        'request_id',
        'facility_id',
        'is_waived',
    ];

    protected $casts = [
        'is_waived' => 'boolean'
    ];

    public $timestamps = false;

    public function requisitionForm()
    {
        return $this->belongsTo(RequisitionForm::class, 'request_id', 'request_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

        public function isWaived()
    {
        return $this->hasMany(RequisitionFee::class, 'waived_facility', 'requested_facility_id');
    }
}

