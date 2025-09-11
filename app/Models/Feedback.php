<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
     protected $table = 'feedback';       

    protected $primaryKey = 'feedback_id';  

    protected $fillable = [
        'email',
        'request_id',
        'system_performance',
        'booking_experience',
        'ease_of_use',
        'useability',
        'additional_feedback',
    ];

    public function requisitionForm()
    {
        return $this->belongsTo(RequisitionForm::class, 'request_id', 'request_id');
    }

    
}
