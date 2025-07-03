<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RequisitionPurpose extends Model
{
    protected $table = "requisition_purposes";
    protected $primaryKey = "purpose_id";
    public $timestamps = false;


    public function requisitionForms()
    {
        return $this->hasMany(RequisitionForm::class, 'purpose_id', 'purpose_id');
    }
    
}
