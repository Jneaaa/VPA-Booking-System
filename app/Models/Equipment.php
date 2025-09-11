<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';
    protected $primaryKey = 'equipment_id';

    protected $fillable = [
        'equipment_name',
        'description',
        'brand',
        'storage_location',
        'category_id',
        'department_id',
        'total_quantity',
        'external_fee',
        'rate_type',
        'status_id',
        'maximum_rental_hour',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    // Relationships

    public function category()
    {
        return $this->belongsTo(LookupTables\EquipmentCategory::class, 'category_id', 'category_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function items()
    {
        return $this->hasMany(EquipmentItem::class, 'equipment_id', 'equipment_id');
    }

    public function images()
    {
        return $this->hasMany(EquipmentImage::class, 'equipment_id', 'equipment_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupTables\AvailabilityStatus::class, 'status_id', 'status_id');
    }

    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'admin_id');
    }

    public function updatedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'updated_by', 'admin_id');
    }

    public function deletedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'deleted_by', 'admin_id');
    }

    public function systemLogs()
    {
        return $this->hasMany(\App\Models\SystemLog::class, 'equipment_id');
    }


    // Scopes
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

}
