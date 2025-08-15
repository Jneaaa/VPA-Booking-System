<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionApproval extends Model
{
    protected $table = "requisition_approvals";

    protected $fillable = [
        'request_id',
        'approved_by',
        'rejected_by',
        'date_approved',
    ];

    protected $casts = [
        'date_approved' => 'datetime',
    ];
    

    // One approval belongs to a single requisition form
    public function requisition()
    {
        return $this->belongsTo(RequisitionForm::class, 'request_id', 'request_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approved_by', 'admin_id');
    }
    
        public function rejectedBy()
    {
        return $this->belongsTo(Admin::class, 'rejected_by', 'admin_id');
    }
}
