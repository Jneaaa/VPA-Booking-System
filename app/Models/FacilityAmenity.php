<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FacilityAmenity extends Model
{
    use HasFactory;

    protected $table = 'facility_amenities';
    protected $primaryKey = 'amenity_id';

    protected $fillable = [
        'amenity_name',
        'amenity_fee',
        'quantity',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }
}
