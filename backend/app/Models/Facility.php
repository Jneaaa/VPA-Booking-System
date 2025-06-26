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
        'category_id',
        'subcategory_id',
        'room_id',
        'location_note',
        'capacity',
        'department_id',
        'is_indoors',
        'rental_fee',
        'company_fee',
        'status_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'last_booked_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_booked_at'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(LookupTables\FacilityCategory::class, 'category_id', 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(LookupTables\FacilitySubcategory::class, 'subcategory_id', 'subcategory_id');
    }

    public function room()
    {
        return $this->belongsTo(RoomDetail::class, 'room_id', 'room_id');
    }

    public function roomDetail()
    {
        return $this->belongsTo(RoomDetail::class, 'room_id', 'room_id');
    }

    public function department()
    {
        return $this->belongsTo(LookupTables\Department::class, 'department_id', 'department_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupTables\AvailabilityStatus::class, 'status_id', 'status_id');
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

    // Scopes
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}