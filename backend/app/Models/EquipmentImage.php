<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentImage extends Model
{
    use HasFactory;

    protected $table = 'equipment_images';
    protected $primaryKey = 'equipment_photo_id';

    protected $fillable = [
        'equipment_id',
        'description',
        'image_type',
        'sort_order',
        'image_url'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'resource_id');
    }
}