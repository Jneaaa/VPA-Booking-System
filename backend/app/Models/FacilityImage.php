<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'department_name',
        'department_description'
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'department_id', 'department_id');
    }

    public function departmentAdmins()
    {
        return $this->hasMany(DepartmentAdmin::class, 'department_id', 'department_id');
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'admin_department')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }
}


