<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminDepartment extends Model
{
    protected $table = 'department_admins';
    protected $primaryKey = 'dept_admin_id';

    protected $fillable = [
        'department_id',
        'admin_id'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
}
