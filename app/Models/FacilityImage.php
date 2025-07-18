<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FacilityImage extends Model
{
    use HasFactory;

    protected $table = 'facility_images';
    protected $primaryKey = 'image_id';

    protected $fillable = [
        'facility_id',
        'image_url',
        'cloudinary_public_id',
        'description',
        'sort_order',
        'image_type' 
    ];


    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }
}


