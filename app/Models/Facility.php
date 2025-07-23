<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'facilities';
    protected $primaryKey = 'facility_id';

    protected $fillable = [
        'facility_name',
        'description',
        'maximum_rental_hour',
        'category_id',
        'subcategory_id',
        'detail_id',
        'location_note',
        'capacity',
        'department_id',
        'location_type',
        'internal_fee',
        'external_fee',
        'rate_type',
        'status_id',
        'last_booked_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'last_booked_at' => 'datetime',
    ];

    // Relationships

    public function amenities()
    {
        return $this->hasMany(FacilityAmenity::class, 'facility_id', 'facility_id');
    }

    public function equipment()
    {
        return $this->hasMany(FacilityEquipment::class, 'facility_id', 'facility_id');
    }

    public function category()
    {
        return $this->belongsTo(LookupTables\FacilityCategory::class, 'category_id', 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(LookupTables\FacilitySubcategory::class, 'subcategory_id', 'subcategory_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupTables\AvailabilityStatus::class, 'status_id', 'status_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function images()
    {
        return $this->hasMany(FacilityImage::class, 'facility_id', 'facility_id');
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

    // Relationships for building and room details

    public function parentFacility()
    {
        return $this->belongsTo(Facility::class, 'parent_facility_id', 'facility_id');
    }

    public function childFacilities()
    {
        return $this->hasMany(Facility::class, 'parent_facility_id', 'facility_id');
    }

    // Scopes
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeBySubcategory($query, $subcategoryId)
    {
        return $query->where('subcategory_id', $subcategoryId);
    }

}