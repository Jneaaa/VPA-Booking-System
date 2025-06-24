<?php

namespace App\LookupTables\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentCategory extends Model
{
    use HasFactory;

    protected $table = 'equipment_categories';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'description'
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'category_id', 'category_id');
    }
}