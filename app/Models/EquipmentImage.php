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
        'is_primary',
        'sort_order',
        'image_url',
        'cloudinary_public_id'
    ];

    protected $casts = [
        'is_primary'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }
}