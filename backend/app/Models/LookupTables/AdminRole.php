<?php

namespace App\LookupTables\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    protected $table = 'admin_roles';
    protected $fillable = ['role_title', 'description'];
    public $timestamps = false;
}
