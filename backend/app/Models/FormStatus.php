<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormStatus extends Model
{
    protected $fillable = [
        'status_name',
    ];

    public function requisitionForms()
    {
        return $this->hasMany(RequisitionForm::class, 'status_id', 'status_id');
    }

}
