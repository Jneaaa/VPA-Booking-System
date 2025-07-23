<?php

namespace App\Models;

use App\Models\LookupTables\FacilitySubcategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomDetail extends Model
{

    protected $table = "facility_details";

    protected $primaryKey = "detail_id";

    use HasFactory;

    protected $fillable = [
        'room_name',
        'building_name',
        'building_code',
        'room_number',
        'floor_level',
    ];

    public function subcategories()
    {
        return $this->hasMany(FacilitySubcategory::class, 'detail_id', 'detail_id');
    }
    public function requisitions()
    {
        return $this->hasMany(RequisitionForm::class, 'detail_id', 'detail_id');
    }
}
