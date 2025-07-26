<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormStatus extends Model
{
    protected $table = "form_statuses";
    protected $primaryKey = "status_id";
    public $timestamps = false;

    public function requisitionForms()
    {
        return $this->hasMany(RequisitionForm::class, 'status_id', 'status_id');
    }

}
