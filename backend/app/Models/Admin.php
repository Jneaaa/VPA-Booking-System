<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Admin extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'admins';
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'photo_url',
        'first_name',
        'last_name',
        'middle_name',
        'role_id',
        'school_id',
        'email',
        'contact_number',
        'hashed_password'
    ];

    protected $hidden = [
        'hashed_password'
    ];

    public function role()
    {
        return $this->belongsTo(AdminRole::class, 'role_id', 'role_id');
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(LookupTables\Department::class, 'admin_departments', 'admin_id', 'department_id')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    public function primaryDepartment()
    {
        return $this->departments()->wherePivot('is_primary', true);
    }

}
