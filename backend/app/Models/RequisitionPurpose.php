<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class RequisitionPurpose extends Model
{
    use HasFactory;
    protected $fillable = ['purpose_name'];

    public function requisitionForms()
    {
        return $this->hasMany(RequisitionForm::class, 'purpose_id', 'purpose_id');
    }
}
