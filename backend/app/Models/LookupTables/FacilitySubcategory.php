<?php

namespace App\Models\LookupTables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RoomDetail;

class FacilitySubcategory extends Model
{
    use HasFactory;

    protected $table = 'facility_subcategories';
    protected $primaryKey = 'subcategory_id';
    public $timestamps = false;

    protected $fillable = [
        'subcategory_name',
    ];

    public function roomDetail()
    {
        return $this->belongsTo(RoomDetail::class, 'room_detail_id', 'room_detail_id');
    }

}
