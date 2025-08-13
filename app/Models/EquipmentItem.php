<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipmentItem extends Model
{
    use HasFactory;


    protected $table = 'equipment_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'equipment_id',
        'item_name',
        'image_url',
        'cloudinary_public_id',
        'status_id',
        'condition_id',
        'barcode_number',
        'item_notes',
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

    public function systemLogs()
    {
        return $this->hasMany(\App\Models\SystemLog::class, 'item_id');
    }

}

