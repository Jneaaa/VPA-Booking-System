<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionApproval extends Model
{
    protected $table = "requisition_approvals";

    protected $fillable = [
        'status',
        'request_id',
        'admin_id',
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

    // One approval is given by a single admin
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
}
