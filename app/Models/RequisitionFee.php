<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionFee extends Model
{
    protected $table = "requisition_fees";
    protected $primaryKey = 'fee_id';

    protected $fillable = [
        'request_id',
        'added_by',
        'label',
        'fee_amount',
        'discount_amount',
        'discount_type',
        'waived_facility',
        'waived_equipment',
        'waived_form'
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'waived_form' => 'boolean'
    ];

    // Eloquent Relationships

    public function requisitionForm()
    {
        return $this->belongsTo(RequisitionForm::class, 'request_id', 'request_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(Admin::class, 'added_by', 'admin_id');
    }

    public function waivedEquipment()
    {
        return $this->belongsTo(RequestedEquipment::class, 'waived_equipment', 'requested_equipment_id');
    }

        public function waivedFacility()
    {
        return $this->belongsTo(RequestedFacility::class, 'waived_facility', 'requested_facility_id');
    }


}