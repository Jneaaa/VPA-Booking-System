<?php

// app/Models/AdminRole.php

namespace App\LookupTables\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{


    protected $table = 'admin_roles';
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_name',
        'role_description'
    ];

    public function admins()
    {
        return $this->hasMany(Admin::class, 'role_id', 'role_id');
    }
}
