<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class EquipmentItem extends Model
{


    protected $table = 'equipment_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'image_url',
        'equipment_id',
        'condition_id',
        'barcode_num',
        'item_notes'
    ];

    // Relationships
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }
}

