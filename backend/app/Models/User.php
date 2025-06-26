<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'first_name',
        'last_name',
        'email',
        'contact_number',
        'organization_name',
        'school_id'
    ];

    public function requisitions()
{
    return $this->hasMany(RequisitionForm::class, 'user_id', 'user_id');
}

    public function uploads()
    {
        return $this->hasMany(UserUpload::class);
    }

    // Additional methods and relationships can be defined here
}
