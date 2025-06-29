<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CalendarEvent extends Model
{
    use HasFactory;

    protected $table = "calendar_events";
    
    protected $primaryKey = "event_id";

    protected $fillable = [
        'request_id',
        'event_title',
        'eventDesc',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function requisitionForms()
    {
        return $this->hasMany(RequisitionForm::class, 'event_id', 'event_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'admin_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by', 'admin_id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(Admin::class, 'deleted_by', 'admin_id');
    }
}
