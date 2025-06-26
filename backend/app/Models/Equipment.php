<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AvailabilityStatus;
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
        'location_note',
        'category_id',
        'subcategory_id',
        'department_id',
        'total_quantity',
        'rental_fee',
        'company_fee',
        'status_id',
        'rate_type',
        'minimum_hour',
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
        return $this->belongsTo(LookupTables\EquipmentCategory::class, 'category_id', 'category_id');
    }

    public function department()
    {
        return $this->belongsTo(LookupTables\Department::class, 'department_id', 'department_id');
    }

    public function type()
    {
        return $this->belongsTo(LookupTables\RateType::class, 'rate_type', 'type_id');
    }

    public function items()
    {
        return $this->hasMany(EquipmentItem::class, 'item_id', 'item_id');
    }

    public function images()
    {
        return $this->hasMany(EquipmentImage::class, 'equipment_id', 'equipment_id')
            ->select(['image_id', 'equipment_id', 'image_url', 'type_id', 'description', 'sort_order']);
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
    public function rateType()
    {
        return $this->belongsTo(\App\Models\LookupTables\RateType::class, 'type_id', 'type_id');
    }

    // Scopes
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }
}
