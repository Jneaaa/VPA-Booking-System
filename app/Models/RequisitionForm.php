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
        'user_id',
        'access_code',
        'num_participants',
        'purpose_id',
        'other_purpose',
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
        'official_receipt_no', 
        'official_receipt_url', 
        'official_receipt_public_id'
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function purpose()
    {
        return $this->belongsTo(RequisitionPurpose::class, 'purpose_id', 'purpose_id');
    }

    public function status()
    {
        return $this->belongsTo(FormStatusCode::class, 'status_id', 'status_id');
    }

    public function finalizedBy()
    {
        return $this->belongsTo(Admin::class, 'finalized_by', 'admin_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(Admin::class, 'closed_by', 'admin_id');
    }

    public function requestedFacilities()
    {
        return $this->hasMany(RequestedFacility::class, 'request_id', 'request_id');
    }

    public function requestedEquipment()
    {
        return $this->hasMany(RequestedEquipment::class, 'request_id', 'request_id');
    }

    public function calendarEvents()
    {
        return $this->belongsTo(CalendarEvent::class, 'event_id', 'event_id');
    }

}
