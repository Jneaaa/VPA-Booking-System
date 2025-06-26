<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserUpload extends Model
{
    protected $fillable = [
        'file_url',
        'cloudinary_public_id',
        'upload_type',
        'upload_token',
        'requisition_id',
    ];

    public function requisitionForm()
    {
        return $this->belongsTo(RequisitionForm::class, 'requisition_id', 'request_id');
    }
}
