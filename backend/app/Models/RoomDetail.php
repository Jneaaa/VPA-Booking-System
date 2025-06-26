<?php

namespace App\Models;

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

    public function facilities()
    {
        return $this->hasMany(Facility::class, 'room_id', 'room_id');
    }
}
