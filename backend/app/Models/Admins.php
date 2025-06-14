<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admins extends Model
{
    use HasApiTokens;
    
    protected $fillable = [
        'username',
        'email',
        'hashed_password',
        'first_name',
        'last_name',
        'middle_name',
        'role_id',
        'school_id',
        'contact_number',
    ];

    protected $primaryKey = 'admin_id';
}
