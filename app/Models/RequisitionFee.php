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
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
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
}