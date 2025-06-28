<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class EquipmentItem extends Model
{


    protected $table = 'equipment_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'equipment_id',
        'item_name',
        'image_url',
        'cloudinary_public_id',
        'condition_id',
        'barcode_number',
        'item_notes',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = [
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    // Relationships
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }
    public function condition()
    {
        return $this->belongsTo(LookupTables\Condition::class, 'condition_id', 'condition_id');
    }
}

