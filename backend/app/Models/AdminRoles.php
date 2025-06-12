<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRoles extends Model
{
    protected $table = 'admin_roles';
    protected $fillable = ['role_title', 'description'];
    public $timestamps = false;
}
