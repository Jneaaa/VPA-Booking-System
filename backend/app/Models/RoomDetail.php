<?php

namespace App\Models;

use App\Models\LookupTables\FacilitySubcategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_id',
        'room_name',
        'building_name',
        'building_code',
        'room_number',
        'floor_level',
    ];

    public function subcategory()
    {
        return $this->hasMany(FacilitySubcategory::class, 'room_id', 'room_id');
    }
    public function requisitions()
    {
        return $this->hasMany(RequisitionForm::class, 'room_id', 'room_id');
    }
}
