<?php

namespace App\Models\LookupTables;

use App\Models\RoomDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FacilitySubcategory extends Model
{
    use HasFactory;

    protected $table = 'facility_subcategories';
    protected $primaryKey = 'subcategory_id';
    public $timestamps = false;

    protected $fillable = [
        'subcategory_name',
    ];

    public function facilityCategory()
    {
        return $this->belongsTo(FacilityCategory::class, 'category_id', 'category_id');
    }
    public function roomDetail()
    {
        return $this->belongsTo(RoomDetail::class, 'room_detail_id', 'room_detail_id');
    }


}
