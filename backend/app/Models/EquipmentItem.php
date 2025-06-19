<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentItem extends Model
{

    use HasFactory;

    protected $table = 'equipment_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'image_url',
        'resource_id',
        'condition',
        'barcode_num',
        'item_notes'
    ];

    // Relationships
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'resource_id', 'resource_id');
    }
}

