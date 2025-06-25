<?php

namespace App\Models\LookupTables;

use Illuminate\Database\Eloquent\Model;

class EquipmentCategory extends Model
{


    protected $table = 'equipment_categories';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    protected $fillable = [
        'category_name',
        'description'
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'category_id', 'category_id');
    }
}