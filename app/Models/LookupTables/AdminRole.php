<?php

// app/Models/AdminRole.php

namespace App\Models\LookupTables;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class AdminRole extends Model
{


    protected $table = 'admin_roles';
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_title',
        'role_description'
    ];

    public function admins()
    {
        return $this->hasMany(Admin::class, 'role_id', 'role_id');
    }
}
