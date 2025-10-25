<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionForm extends Model
{
    protected $table = "requisition_forms";
    protected $primaryKey = 'request_id';
    use HasFactory;
    protected $fillable = [
        'user_type',
        'first_name',
        'last_name',
        'email',
        'school_id',
        'organization_name',
        'contact_number',
        'num_participants',
        'num_chairs',
        'num_tables',
        'purpose_id',
        'additional_requests',
        'status_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'is_late',
        'late_penalty_fee',
        'returned_at',
        'is_finalized',
        'finalized_at',
        'finalized_by',
        'is_closed',
        'closed_at',
        'closed_by',
        'endorser',
        'date_endorsed',
        'tentative_fee',
        'approved_fee',
        'calendar_title',
        'calendar_description',
        'access_code',
        'formal_letter_url',
        'formal_letter_public_id',
        'facility_layout_url',
        'facility_layout_public_id',
        'proof_of_payment_url',
        'proof_of_payment_public_id'
    ];
    protected $casts = [
        'start_date' => 'string',
        'end_date' => 'string',
        'start_time' => 'string',
        'end_time' => 'string',
        'returned_at' => 'datetime',
        'finalized_at' => 'datetime',
        'closed_at' => 'datetime',
        'date_endorsed' => 'datetime',
        'is_late' => 'boolean',
        'is_finalized' => 'boolean',
        'is_closed' => 'boolean',
        'tentative_fee' => 'decimal:2',
        'approved_fee' => 'decimal:2',
    ];
    // Relationships
    public function status()
    {
        return $this->belongsTo(FormStatus::class, 'status_id', 'status_id');
    }
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'request_id', 'request_id');
    }
    public function purpose()
    {
        return $this->belongsTo(RequisitionPurpose::class, 'purpose_id', 'purpose_id');
    }
    public function formStatus()
    {
        return $this->belongsTo(FormStatus::class, 'status_id');
    }
    public function requestedFacilities()
    {
        return $this->hasMany(RequestedFacility::class, 'request_id');
    }
    public function requestedEquipment()
    {
        return $this->hasMany(RequestedEquipment::class, 'request_id');
    }
    public function requisitionApprovals()
    {
        return $this->hasMany(RequisitionApproval::class, 'request_id');
    }
    public function requisitionComments()
    {
        return $this->hasMany(RequisitionComment::class, 'request_id', 'request_id');
    }
    public function requisitionFees()
    {
        return $this->hasMany(RequisitionFee::class, 'request_id', 'request_id');
    }
    public function finalizedBy()
    {
        return $this->belongsTo(Admin::class, 'finalized_by', 'admin_id');
    }
    public function closedBy()
    {
        return $this->belongsTo(Admin::class, 'closed_by', 'admin_id');
    }
}
