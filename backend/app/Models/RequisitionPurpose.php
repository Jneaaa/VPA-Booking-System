<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionPurpose extends Model
{
    protected $fillable = [
        'purpose_name',
    ];

    public function requisitionForms()
    {
        return $this->hasMany(RequisitionForm::class, 'purpose_id', 'purpose_id');
    }
    
}
