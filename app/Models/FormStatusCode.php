<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormStatusCode extends Model
{
    protected $table = "form_status_codes";
    protected $primaryKey = "status_id";
    public $timestamps = false;

    public function requisitionForms()
    {
        return $this->hasMany(RequisitionForm::class, 'status_id', 'status_id');
    }

}
