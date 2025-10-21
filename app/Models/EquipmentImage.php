<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipmentImage extends Model
{
    use HasFactory;

    protected $table = 'equipment_images';
    protected $primaryKey = 'image_id';

    protected $fillable = [
        'equipment_id',
        'description',
        'sort_order',
        'image_url',
        'cloudinary_public_id',
        'image_type'
    ];

    /* Migration format:
    Columns:
        image_id bigint UN AI PK 
        equipment_id bigint UN 
        image_url varchar(255) 
        cloudinary_public_id varchar(255) 
        description varchar(80) 
        sort_order int 
        image_type enum('Primary','Secondary') 
        created_at timestamp 
        updated_at timestamp
    */

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }
}