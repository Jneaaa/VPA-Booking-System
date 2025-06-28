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
        'request_id',
    ];

    public function requisitionForm()
    {
        return $this->belongsTo(RequisitionForm::class, 'request_id', 'request_id');
    }
    public function scopeLetters($query)
    {
        return $query->where('upload_type', 'Letter');
    }

    public function scopeRoomSetups($query)
    {
        return $query->where('upload_type', 'Room Setup');
    }

}
