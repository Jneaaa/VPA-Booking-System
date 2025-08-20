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
        'photo_public_id',
        'wallpaper_url',
        'wallpaper_public_id',
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
        'hashed_password',
        'role_id'  // Hide role_id since it will be included in the role object
    ];

    protected $with = ['role'];  // Always load the role relationship

    // ----- Role Assignment ----- //
    public function role()
    {
        return $this->belongsTo(LookupTables\AdminRole::class, 'role_id', 'role_id');
    }


    // ----- Department Assignments ----- //
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'admin_departments', 'admin_id', 'department_id')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }
    public function primaryDepartment()
    {
        return $this->departments()->wherePivot('is_primary', true);
    }

    // ----- Requisition Management ----- //

    public function finalizedByAdmin()
    {
        return $this->hasMany(RequisitionForm::class, 'finalized_by', 'admin_id');
    }

    public function closedByAdmin()
    {
        return $this->hasMany(RequisitionForm::class, 'closed_by', 'admin_id');
    }
    
        public function adminApprovals()
    {
        return $this->hasMany(RequisitionApproval::class, 'approved_by', 'admin_id');
    }

    public function adminRejections()
    {
        return $this->hasMany(RequisitionApproval::class, 'rejected_by', 'admin_id');
    }

    // ----- Equipment Management ----- //
    public function createEquipment()
    {
        return $this->hasMany(Equipment::class, 'created_by', 'admin_id');
    }
    public function updateEquipment()
    {
        return $this->hasMany(Equipment::class, 'updated_by', 'admin_id');
    }
    public function deleteEquipment()
    {
        return $this->hasMany(Equipment::class, 'deleted_by', 'admin_id');
    }

    // ----- Equipment Items Management ----- //
    public function createEquipmentItems()
    {
        return $this->hasMany(EquipmentItem::class, 'created_by', 'admin_id');
    }
    public function updateEquipmentItems()
    {
        return $this->hasMany(Equipment::class, 'updated_by', 'admin_id');
    }
    public function deleteEquipmentItems()
    {
        return $this->hasMany(Equipment::class, 'deleted_by', 'admin_id');
    }

    // ----- Facility Management ----- //
    public function createFacility()
    {
        return $this->hasMany(Facility::class, 'created_by', 'admin_id');
    }
    public function updateFacility()
    {
        return $this->hasMany(Facility::class, 'updated_by', 'admin_id');
    }
    public function deleteFacility()
    {
        return $this->hasMany(Facility::class, 'deleted_by', 'admin_id');
    }

    public function systemLogs()
    {
        return $this->hasMany(SystemLog::class, 'admin_id', 'admin_id');
    }

    // relationship with RequisitionComment
    public function comments()
    {
        return $this->hasMany(RequisitionComment::class, 'admin_id', 'admin_id');
    }
}