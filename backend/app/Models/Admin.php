<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'admins';

    protected $primaryKey = 'admin_id';
    public $timestamps = true;
    
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

    protected $hidden = [
        'hashed_password',
    ];

    
}
