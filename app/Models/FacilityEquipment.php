<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FacilityEquipment extends Model
{
    use HasFactory;
    protected $table = "facility_equipment";
    protected $primaryKey = 'facility_equipment_id';

    public $timestamps = false; 


    // Relationships
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }


}
